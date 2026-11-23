/**
 * POST /api/enquiry - the enquiry form handler for the Cloudflare build.
 *
 * Replaces WordPress's admin-post.php handler, with the same protections:
 * a honeypot field and a timestamp trap. Both traps report success, since
 * telling a bot it failed only teaches it what to change.
 *
 * Delivery (configure in Cloudflare Pages > Settings):
 *   ENQUIRIES        KV namespace binding. Every enquiry is stored here
 *                    first, so nothing is lost if email fails.
 *   RESEND_API_KEY   Secret. Sends the notification email via resend.com.
 *   ENQUIRY_TO       Where notifications go, e.g. sales@riancullets.com
 *   ENQUIRY_FROM     Verified sender, e.g. "Rian Cullet <enquiries@riancullets.com>"
 *
 * At least one of ENQUIRIES or RESEND_API_KEY must be set; otherwise the
 * visitor is told the form is unavailable rather than silently dropped.
 *
 * Responds with JSON when the request asks for it (the enhanced form in
 * /enquiry.js does), and with a 303 redirect back to the contact page for
 * a plain no-JavaScript form post.
 */

const FIELDS = {
  name: { label: 'Name', required: true, max: 120 },
  company: { label: 'Company', required: false, max: 160 },
  email: { label: 'Email', required: true, max: 200 },
  phone: { label: 'Phone', required: false, max: 40 },
  requirement: { label: 'Material requirement', required: false, max: 40 },
  message: { label: 'Message', required: true, max: 5000 },
};

const REQUIREMENTS = {
  factory: 'Factory cullet',
  foreign: 'Foreign / post-consumer cullet',
  flint: 'Flint (white) cullet',
  multiple: 'More than one',
  other: 'Other / not sure yet',
};

const MIN_SECONDS = 3;

export async function onRequestPost({ request, env }) {
  const wantsJson = (request.headers.get('Accept') || '').includes('application/json');

  let form;
  try {
    form = await request.formData();
  } catch {
    return respond(wantsJson, request, false, ['The form could not be read. Please try again.'], 400);
  }

  // Spam traps: pretend success.
  const honeypot = String(form.get('rc_website') || '').trim();
  const started = Number(form.get('rc_time') || 0);
  const now = Math.floor(Date.now() / 1000);
  if (honeypot || (started > 0 && now - started < MIN_SECONDS)) {
    return respond(wantsJson, request, true);
  }

  const data = {};
  const errors = [];

  for (const [key, field] of Object.entries(FIELDS)) {
    const value = String(form.get(key) || '').trim().slice(0, field.max);
    data[key] = value;
    if (field.required && !value) {
      errors.push(`${field.label} is required.`);
    }
  }

  if (data.email && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(data.email)) {
    errors.push('Please enter a valid email address.');
  }

  if (data.requirement && !REQUIREMENTS[data.requirement]) {
    data.requirement = '';
  }

  if (errors.length) {
    return respond(wantsJson, request, false, errors, 422);
  }

  if (!env.ENQUIRIES && !env.RESEND_API_KEY) {
    console.error('enquiry: no ENQUIRIES KV binding and no RESEND_API_KEY configured');
    return respond(wantsJson, request, false, ['Online enquiries are temporarily unavailable. Please contact us directly.'], 503);
  }

  const record = {
    ...data,
    requirement_label: REQUIREMENTS[data.requirement] || '',
    received_at: new Date().toISOString(),
    ip: request.headers.get('CF-Connecting-IP') || '',
    country: request.cf?.country || '',
    page: request.headers.get('Referer') || '',
  };

  let stored = false;
  let mailed = false;

  if (env.ENQUIRIES) {
    try {
      const key = `enquiry:${record.received_at}:${crypto.randomUUID()}`;
      await env.ENQUIRIES.put(key, JSON.stringify(record));
      stored = true;
    } catch (err) {
      console.error('enquiry: KV write failed', err);
    }
  }

  if (env.RESEND_API_KEY && env.ENQUIRY_TO && env.ENQUIRY_FROM) {
    try {
      mailed = await sendMail(env, record);
    } catch (err) {
      console.error('enquiry: mail failed', err);
    }
  }

  if (!stored && !mailed) {
    return respond(wantsJson, request, false, ['Your enquiry could not be sent just now. Please try again shortly or contact us directly.'], 502);
  }

  return respond(wantsJson, request, true);
}

export function onRequestGet({ request }) {
  return Response.redirect(new URL('/contact/', request.url).toString(), 302);
}

async function sendMail(env, r) {
  const rows = [
    ['Name', r.name],
    ['Company', r.company],
    ['Email', r.email],
    ['Phone', r.phone],
    ['Requirement', r.requirement_label],
    ['Page', r.page],
    ['Received', r.received_at],
  ].filter(([, v]) => v);

  const html =
    '<h2>New enquiry from the website</h2><table cellpadding="6">' +
    rows.map(([k, v]) => `<tr><th align="left">${esc(k)}</th><td>${esc(v)}</td></tr>`).join('') +
    `</table><h3>Message</h3><p>${esc(r.message).replace(/\n/g, '<br>')}</p>`;

  const text =
    rows.map(([k, v]) => `${k}: ${v}`).join('\n') + `\n\nMessage:\n${r.message}\n`;

  const res = await fetch('https://api.resend.com/emails', {
    method: 'POST',
    headers: {
      Authorization: `Bearer ${env.RESEND_API_KEY}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      from: env.ENQUIRY_FROM,
      to: env.ENQUIRY_TO.split(',').map((s) => s.trim()).filter(Boolean),
      reply_to: r.email,
      subject: `Website enquiry: ${r.name}${r.company ? ` (${r.company})` : ''}`,
      html,
      text,
    }),
  });

  if (!res.ok) {
    console.error('enquiry: resend responded', res.status, await res.text());
    return false;
  }
  return true;
}

function respond(wantsJson, request, ok, errors = [], status = 200) {
  if (wantsJson) {
    return new Response(JSON.stringify({ ok, errors }), {
      status: ok ? 200 : status,
      headers: { 'Content-Type': 'application/json', 'Cache-Control': 'no-store' },
    });
  }
  const url = new URL('/contact/', request.url);
  url.searchParams.set('enquiry', ok ? 'sent' : 'error');
  url.hash = 'rc-enquiry';
  return Response.redirect(url.toString(), 303);
}

function esc(s) {
  return String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

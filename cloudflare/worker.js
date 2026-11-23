/**
 * Cloudflare Worker entry point.
 *
 * Serves the static export in cloudflare/public/ (Workers static assets,
 * which also honour _headers and _redirects) and routes the enquiry form
 * to the same handler the Pages build uses.
 */
import { onRequestPost, onRequestGet } from './functions/api/enquiry.js';

export default {
  async fetch(request, env) {
    const url = new URL(request.url);

    if (url.pathname === '/api/enquiry' || url.pathname === '/api/enquiry/') {
      return request.method === 'POST'
        ? onRequestPost({ request, env })
        : onRequestGet({ request });
    }

    if (url.pathname.startsWith('/api/')) {
      return new Response('Not found', { status: 404 });
    }

    return env.ASSETS.fetch(request);
  },
};

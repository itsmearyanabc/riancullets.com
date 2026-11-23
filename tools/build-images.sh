#!/usr/bin/env bash
# Generate responsive AVIF + WebP variants of the theme photography.
#
# Writes <name>-<width>w.avif and <name>-<width>w.webp next to the
# originals in assets/img/. rc_figure() discovers them automatically and
# builds a srcset, so phones download a ~40KB hero instead of ~300KB.
#
# Requires ffmpeg with libaom-av1 and libwebp. Re-run after replacing a
# photograph. Existing variants are overwritten.
set -euo pipefail

IMG="$(cd "$(dirname "$0")/.." && pwd)/wp-content/themes/rian-cullet/assets/img"

# name:widths (comma separated). The original file is always the largest.
SPECS=(
  "hero-slide-1:640,1024,1600"
  "hero-slide-2:640,1024,1600"
  "hero-slide-3:640,1024,1600"
  "hero-slide-4:640,1024,1600"
  "intro-cullet-macro:480,800"
  "factory-cullet:480,800"
  "foreign-cullet:480,800"
  "flint-cullet:480,800"
  "clear-glass:480,800"
  "amber-glass:480,800"
  "green-glass:480,800"
  "processing-sorting:640,1200"
)

for spec in "${SPECS[@]}"; do
  name="${spec%%:*}"
  widths="${spec#*:}"
  src="$IMG/$name.jpg"
  [ -f "$src" ] || { echo "skip $name (no jpg)"; continue; }

  IFS=',' read -ra ws <<< "$widths"
  for w in "${ws[@]}"; do
    ffmpeg -hide_banner -loglevel error -y -i "$src" -vf "scale=${w}:-2:flags=lanczos" \
      -c:v libaom-av1 -still-picture 1 -crf 33 -b:v 0 -cpu-used 4 "$IMG/$name-${w}w.avif"
    ffmpeg -hide_banner -loglevel error -y -i "$src" -vf "scale=${w}:-2:flags=lanczos" \
      -c:v libwebp -quality 74 -compression_level 6 "$IMG/$name-${w}w.webp"
    echo "$name-${w}w"
  done
done

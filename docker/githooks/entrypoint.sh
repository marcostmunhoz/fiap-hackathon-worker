#!/bin/sh

set -e

cd /tmp/.git/hooks

for hook in ../../hooks/*; do
  if [ -f "$hook" ]; then
    cp -f "$hook" /tmp/.git/hooks/
    chmod +x "/tmp/.git/hooks/$(basename "$hook")"
    echo "Installed hook: $(basename "$hook")"
  fi
done

echo 'Git hooks installation complete!'

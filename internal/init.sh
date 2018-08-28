#!/bin/sh

echo "permission changing..."
chmod 0777 smarty_cache
chmod 0777 smarty_templates_c
chmod 0777 app/artwork
chmod 0777 app/log
echo "OK"

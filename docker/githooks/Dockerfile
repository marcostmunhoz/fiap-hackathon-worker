FROM busybox:latest

COPY ./hooks /tmp/hooks
COPY entrypoint.sh /tmp

ENTRYPOINT ["sh", "/tmp/entrypoint.sh"]

# FIAP Hackathon - Worker

Repositório responsável pela aplicação worker, cuja lida com o processamento dos vídeos (extração de frames e compactação em arquivo zip) e comunicação para o usuário (e-mails de sucesso e erro), disparados por meio de mensagens postadas em um determinado tópico do Pub/Sub.
Também contém a infraestrutura específica dessa aplicação (Cloud Run e recursos auxiliares) (para maiores informações, consultar a pasta [terraform](./terraform))

## Como utilizar

1. Clone o repositório
2. Construa e suba os containeres (`docker compose up -d`)
3. O worker estará executando em background, podendo ser consultado via `docker compose logs -f`

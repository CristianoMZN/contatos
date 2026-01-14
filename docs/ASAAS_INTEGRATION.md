# Integração ASAAS

## Visão Geral

Esta integração adiciona um gateway de pagamentos para assinaturas premium utilizando a API do ASAAS. O fluxo segue a Clean Architecture: o caso de uso `UpgradeSubscriptionUseCase` orquestra o billing através do `PaymentGatewayInterface`, implementado por `AsaasPaymentService`, e persiste o estado no Firestore pelo `FirestoreSubscriptionRepository`. Webhooks do ASAAS são tratados pelo `HandleAsaasWebhookUseCase` e expostos via `AsaasWebhookController`.

## Variáveis de Ambiente

- `ASAAS_API_KEY`: token de acesso à API.
- `ASAAS_BASE_URL` (opcional): default `https://sandbox.asaas.com/api/v3`.
- `ASAAS_WEBHOOK_SECRET` (reservado para futura validação de assinatura).
- `FIREBASE_PROJECT_ID`, `FIREBASE_DATABASE_URL`, `FIREBASE_STORAGE_BUCKET`, `GOOGLE_APPLICATION_CREDENTIALS`: usados pelo Firestore.

## Endpoints

- `POST /webhooks/asaas`: recebe eventos (`PAYMENT_CONFIRMED`, `SUBSCRIPTION_ACTIVATED`, `SUBSCRIPTION_CANCELED`, etc) e sincroniza o status da assinatura no Firestore.

## Testes

- Testes unitários cobrem mapeamento de status (`PaymentResult`) e processamento de webhooks (`HandleAsaasWebhookUseCase`) com repositório em memória.

## Feature Flags

`FeatureFlagService` expõe limites de premium (contatos, storage) baseados na assinatura ativa, permitindo aplicar feature flags em controllers e views.

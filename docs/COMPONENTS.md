# Componentes de Apresentação (Symfony UX)

Este documento descreve os componentes criados para a camada de apresentação utilizando **Symfony UX**, **Twig Components**, **Live Components** e **Turbo Frames**.

## Componentes

### `public_contact_list`
- **Tipo:** Live Component (`src/Presentation/Twig/Components/PublicContactListComponent.php`)
- **Template:** `templates/components/public_contact_list.html.twig`
- **Props:**
  - `search` (string) – texto de busca em tempo real
  - `categoryId` (?string) – filtro de categoria (ex.: `business`, `family`, `favorites`)
  - `latitude`/`longitude` (?float) – filtro geográfico opcional
  - `radiusKm` (?float) – raio para geofence (km)
  - `viewMode` (grid|list) – alterna visualização cards/lista
  - `limit` (int) – quantidade máxima de itens
- **Dados:** consome `ListPublicContactsUseCase` + `ContactListFilter` (camada Application).
- **Fluxo:** renderizado dentro de um Turbo Frame; a cada alteração de filtros o frame é recarregado (SSR), mantendo compatibilidade com Live Components.

### `contact_card`
- **Tipo:** Twig Component (`src/Presentation/Twig/Components/ContactCardComponent.php`)
- **Template:** `templates/components/contact_card.html.twig`
- **Props:**
  - `contact` (Domain `Contact`)
  - `viewMode` (grid|list)
- **Recursos:** badges de favorito/público, e-mail/mailto, telefone com formatação brasileira, coordenadas, notas truncadas.

## Páginas

### Agenda pública
- **Controller:** `ContactBrowserController` (`/contacts/public`)
- **Template:** `templates/contacts/public.html.twig`
- **Features:**
  - Busca em tempo real com debounce
  - Filtro de categoria e raio (km)
  - Geolocalização opcional (preenche `lat/lng` e ativa filtro)
  - Alternância de view (cards/lista)
  - Turbo Frame para SSR dinâmico (`/contacts/public/frame`)
  - Tema claro/escuro via toggle (persistido em `localStorage`)
  - Máscara dinâmica para campo de telefone/busca

## Como usar
1. Acesse `/contacts/public` para a UI SSR com Turbo Frames.
2. O campo de busca, seletor de categoria, raio e view mode disparam recarga do frame.
3. O botão **Usar minha localização** solicita permissão de GPS e envia `lat/lng` ao componente.
4. O switch **Tema escuro** grava a preferência e aplica `data-bs-theme`.

## Observações
- O fallback de atualização do frame usa `fetch` caso a API do Turbo não esteja disponível.
- Todos os outputs são escapados pelo Twig; apenas dados formatados do domínio são exibidos.
- Os componentes permanecem desacoplados da camada de infraestrutura, consumindo apenas casos de uso e DTOs da camada de aplicação.

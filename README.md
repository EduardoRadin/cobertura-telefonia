# Sistema de Cobertura Telefônica

Sistema desenvolvido em Laravel + Vue.js para consulta de cobertura telefônica através de webscraping e APIs.

## Funcionalidades

- **Webscraping do site Ligue Aí**: Extrai dados de cobertura da tabela do site oficial
- **Integração com API TIP Brasil**: Consulta dados de cobertura via API
- **Integração com ViaCEP**: Busca informações de CEP e localiza cobertura correspondente
- **Sistema de busca**: Pesquisa por cidade, estado ou CEP
- **Interface moderna**: Desenvolvida com Vue.js e Tailwind CSS

## Instalação

1. Clone o repositório
2. Instale as dependências:
   ```bash
   composer install
   npm install
   ```

3. Configure o arquivo `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Compile os assets:
   ```bash
   npm run build
   ```

5. Inicie o servidor:
   ```bash
   php artisan serve
   ```

## Uso

1. Acesse `http://localhost:8000`
2. Clique em "Importar Ligue Aí" para extrair dados do site oficial
3. Clique em "Importar TIP Brasil" para buscar dados da API
4. Use a barra de pesquisa para buscar por:
   - Nome da cidade
   - Sigla do estado (ex: SP, RJ)
   - CEP (formato: 12345-678)

## APIs Disponíveis

### Importação de Dados
- `POST /scrape/ligue-ai` - Importa dados do site Ligue Aí
- `POST /scrape/tip-brasil` - Importa dados da API TIP Brasil

### Consulta de Dados
- `GET /search?query=termo&type=all` - Busca por termo
- `GET /viacep/{cep}` - Consulta CEP no ViaCEP
- `GET /ligue-ai-data` - Lista todos os dados do Ligue Aí
- `GET /tip-brasil-data` - Lista todos os dados da TIP Brasil

## Estrutura do Projeto

```
app/
├── Http/Controllers/
│   ├── CoverageController.php    # Controller principal
│   └── ScrapingController.php    # Controller de webscraping
├── Models/
│   ├── LigueAiCoverage.php       # Modelo Ligue Aí
│   └── TipBrasilCoverage.php     # Modelo TIP Brasil
resources/
├── js/
│   ├── app.js                    # Arquivo principal Vue
│   └── components/
│       └── App.vue               # Componente principal
└── views/
    └── coverage/
        └── index.blade.php       # View principal
routes/
└── web.php                       # Rotas da aplicação
```

## Tecnologias Utilizadas

- **Backend**: Laravel 12
- **Frontend**: Vue.js 3
- **Styling**: Tailwind CSS
- **Webscraping**: Symfony DomCrawler
- **HTTP Client**: Guzzle HTTP
- **Build Tool**: Vite

## Observações

- O sistema funciona com dados em memória (sem banco de dados)
- Os dados são perdidos ao reiniciar o servidor
- Para persistência, configure um banco de dados e execute as migrations

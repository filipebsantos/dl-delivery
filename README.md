# DL Delivery v2.0

Sistema para auxiliar entregadores e atendentes no cadastro e consulta de localizações de entrega em regiões com endereços imprecisos ou inexistentes.

---

## 🚚 Problema que resolve

Clientes frequentemente têm dificuldades para descrever com precisão onde moram, seja por que as ruas não receberam nomeclatura oficial por parte da prefeitura, inexistência de norma ou falta de fiscalização da prefeitura quanto a correta numeração dos imóveis ou dados imprecisos no Google Maps. Isso atrasa as entregas, sobrecarrega os atendentes e prejudica a experiência geral.

**DL Delivery** resolve isso ao permitir:
- Registro de coordenadas GPS e fotos da fachada do imóvel feita pelo entregador via navegador mobile.
- Consulta rápida das localizações por parte do atendente via sistema web.
- Agrupamento de clientes em rotas de entrega flexíveis e personalizadas.

---

## ⚙️ Sobre a versão 2.0

Reescrita completa do sistema com foco em arquitetura limpa e escalável.

Principais mudanças:
- Separação clara entre front-end, back-end e regras de negócio.
- Backend estruturado com **Domain-Driven Design (DDD)**.
- **API em PHP puro**, com testes desde o início.
- Controle de acesso por tipo de usuário (operador e administrador).
- Entregadores agora escolhem os clientes que irão atender nas rotas, com maior autonomia.

---

## 🧱 Stack técnica

- **Frontend**: Vue.js + TailwindCSS (foco mobile-first)
- **Backend**: PHP 8.3 (sem framework) + testes automatizados
- **Banco de dados**: Microsoft SQL Server ou SQLite3
- **Arquitetura**: DDD + C4Model + UML
- **Ambiente de dev**: Docker + DevContainers


## 📁 Estrutura do projeto (parcial)

```txt
/src
    /api/src            → Código da API PHP (com separação por contexto)
        /Domain         → Entidades, VOs, interfaces
        /Application    → Serviços de aplicação
        /Infrastructure → Repositórios e integrações
        /Interface      → Camada de apresentação da API
        /Exception      → Tratamento e controle de erros
    /tests         → Testes automatizados

    /web        → Interface web e mobile
```

---

## 👤 Usuários e Permissões

- **Entregador**: Inclui clientes na rota de entrega, cadastra e edita localizações dos clientes.
- **Operador**: Cadastra e edita clientes, localizações e rotas, pode excluir rotas.
- **Administrador**: Pode excluir clientes, localizações e gerenciar usuários.
- O próprio usuário pode atualizar seus dados e senha.
- Credenciais são armazenadas com hash seguro (bcrypt/Argon2).

---

## 🛣️ Contextos principais

- **Usuários**: Acesso ao sistema e permissões.
- **Clientes**: Dados dos clientes e suas localizações.
- **Rotas**: Agrupamento de clientes para entrega (flexível e selecionável pelo entregador).

---

## 📌 Roadmap

- [x] Estrutura inicial do projeto
- [x] Módulo de usuários com autenticação e permissões
- [x] Cadastro de usuários
- [ ] Cadastro de clientes com múltiplas localizações
- [ ] Criação e consulta de rotas
- [ ] Captura de coordenadas e fotos pelo entregador
- [ ] Interface web para o atendente
- [ ] Mobile-first para entregadores

---

## 📍 Status do projeto

🚧 **Em desenvolvimento ativo. Não está pronto para produção.**

---

## 📄 Licença

Licença a definir. Por enquanto, uso interno.
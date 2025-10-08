<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

Documentação da API - Gestão de Empresas, Departamentos e Colaboradores

1\. Empresa (Company)
=====================

1.1 Cadastro de Empresa
-----------------------

- Rota: POST/api/company/auth/register
- Descrição: Cadastra uma nova empresa e retorna token de autenticação.
- Body (JSON):

```json
{
  "name": "Minha Empresa",
  "email": "empresa@email.com",
  "password": "12345678",
  "cnpj": "12345678000190",
  "cnae": "6201-5/01"
}
```

1.2 Login da Empresa
--------------------

- Rota: POST/api/company/auth/login
- Body (JSON):
```json

{ 
"email":"empresa@email.com", 
"password":"12345678"
}
```

1.3 Logout da Empresa
---------------------

- Rota: POST/api/company/auth/logout
- Middleware: auth:company
- Body:Nenhum

1.4 Atualizar Perfil da Empresa
-------------------------------

- Rota: PATCH/api/company/profile
- Middleware: auth:company
- Body (JSON):

```json
{ 
"primary_color":"#FF0000", 
"secondary_color": "#00FF00", 
"text_color": "#000000", 
"button_color":"#FFFFFF", 
"font": "Arial"
}
```

1.5 Upload de Assets (Logo/Banner)
----------------------------------

- Rota: PATCH/api/company/assets
- Middleware: auth:company
- Body:multipart/form-data com logo e/ou banner.

2\. Departamento (Department)
=============================

2.1 Cadastro de Departamento
----------------------------

- Rota: POST/api/company/department
- Middleware: auth:company
- Body(JSON):
```json
{ 
"name":"Financeiro", 
"description": "Departamento financeiro"
}
```


2.2 Listar Departamentos
------------------------

- Rota: GET/api/company/department
- Middleware: auth:company
- Body:Nenhum

3\. Colaborador (Collaborator)
==============================

3.1 Cadastro de Colaborador
---------------------------

- Rota: POST/api/company/collaborator
- Middleware: auth:company
- Body(JSON):
```json
{ 
"name":"João Silva", 
"email":"joao.silva@email.com", 
"cpf":"12345678900", 
"password": "12345678",
"birth_date": "1990-01-01", 
"photo":null, 
"departments": [9, 7]
}
```
3.2 Login de Colaborador
------------------------

- Rota: POST/api/collaborator/auth/login
- Body (JSON):
```json
{ 
"email":"joao.silva@email.com", 
"password":"12345678"
}
```

3.3 Logout de Colaborador
-------------------------

- Rota: POST/api/collaborator/auth/logout
- Middleware:auth:collaborator
- Body: Nenhum

Observações Gerais
==================

\- Todos os endpoints autenticados exigem Bearer Token no header Authorization.
- Validações retornam 422 com objeto detalhado de erros.
- - Erros de autenticação retornam 401.
- - Erros inesperados do servidor retornam 500.

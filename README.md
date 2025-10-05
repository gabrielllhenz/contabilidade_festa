# 🍻 Festa Outubro - Sistema de Controle de Festa

Um sistema web simples e gratuito, feito em **PHP**, **MySQL**, **HTML**, **CSS** e **JavaScript**, para ajudar na **organização de festas** entre amigos.  
Ele permite gerenciar **entradas (convidados)**, **saídas (despesas e bebidas)** e **contabilidade geral**, de forma segura e acessível de qualquer lugar com internet.

---

## 🚀 Funcionalidades

### 🧍‍♂️ Entradas
- Cadastro e listagem de convidados  
- Campos: nome, valor pago, observação  
- Cálculo automático do total pago  

### 🍾 Saídas (Despesas e Bebidas)
- CRUD completo de despesas e bebidas  
- Campos calculados automaticamente (ex: total = valor unidade × quantidade)  
- Seleção de forma de pagamento (Pix ou Dinheiro)  
- Marcação de status “Pago / Não Pago”  

### 💰 Contabilidade
- Total de bebidas  
- Total recebido  
- Total de despesas  
- Totais por forma de pagamento (Pix / Dinheiro)  
- Saldo automático entre receitas e despesas  

### 🔒 Login Seguro
- Sistema de autenticação simples com hash de senha (`password_hash` / `password_verify`)  
- Apenas usuários autorizados podem acessar e editar dados  

---

## 🛠️ Tecnologias Utilizadas

- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP (mysqli)  
- **Banco de Dados:** MySQL  
- **Hospedagem gratuita:** InfinityFree (ou local com XAMPP / Laragon)  

---

## 🧩 Estrutura do Projeto


---

## ⚙️ Como Instalar (Localmente)

1. Instale o [XAMPP](https://www.apachefriends.org/pt_br/index.html).  
2. Coloque o projeto dentro da pasta `htdocs` (ex: `C:\xampp\htdocs\festa`).  
3. Inicie **Apache** e **MySQL** no painel do XAMPP.  
4. No phpMyAdmin, crie o banco:
   ```sql
   CREATE DATABASE festa_db;
$host = "localhost";
$dbname = "festa_db";
$username = "root";
$password = "";
$port = 3306 conforme seu MySQL

http://localhost/festa

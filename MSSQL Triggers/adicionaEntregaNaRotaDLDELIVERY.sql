CREATE TRIGGER [dbo].[adicionaEntregaNaRotaDLDELIVERY] ON [dbo].[Venda_Entrega_Saida] 
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @CodigoEntrega INT,
            @CodigoFuncionario INT,
            @CodigoVenda INT,
            @CodigoCadastro INT,
            @client_id INT,
            @deliveryman INT,
            @route_id INT,
            @client_name NVARCHAR(255),
            @phonenumber NVARCHAR(20);

    -- Pegue os valores dos campos 'CodigoEntrega' e 'CodigoFuncionario'
    SELECT @CodigoEntrega = i.CodigoEntrega, @CodigoFuncionario = i.CodigoFuncionario
    FROM INSERTED i;

    -- Salve o valor de 'CodigoFuncionario' na variável @deliveryman
    SET @deliveryman = @CodigoFuncionario;

    -- Busque o 'CodigoVenda' na tabela 'Venda_Entrega' e o número de celular
    SELECT @CodigoVenda = ve.CodigoVenda, @phonenumber = ve.Celular
    FROM Venda_Entrega ve
    WHERE ve.CodigoEntrega = @CodigoEntrega;

    -- Busque o 'CodigoCadastro' na tabela 'Venda'
    SELECT @CodigoCadastro = v.CodigoCadastro
    FROM Venda v
    WHERE v.CodigoVenda = @CodigoVenda;

    -- Busque o 'CodigoCliente' na tabela 'Cliente'
    SELECT @client_id = c.CodigoCliente
    FROM Cliente c
    WHERE c.CodigoCadastro = @CodigoCadastro;

    -- Verifique no banco DLDELIVERY
    EXEC('USE DLDELIVERY');

    -- Verifique se existe um registro PENDENTE na tabela 'routes' para o @deliveryman
    SELECT @route_id = r.id
    FROM DLDELIVERY.dbo.routes r
    WHERE r.deliveryman = @deliveryman AND r.status = 'PENDENTE';

    -- Verifique se o cliente existe na tabela 'clients'
    IF NOT EXISTS (SELECT 1 FROM DLDELIVERY.dbo.clients WHERE id = @client_id)
    BEGIN
        -- Busque o nome do cliente na tabela 'Cadastros'
        EXEC('USE INOVAFARMABANCO');

        SELECT @client_name = cad.Nome
        FROM Cadastro cad
        INNER JOIN Cliente cli ON cli.CodigoCadastro = cad.CodigoCadastro
        WHERE cli.CodigoCliente = @client_id;

        -- Insira o novo cliente na tabela 'clients'
        EXEC('USE DLDELIVERY');

        INSERT INTO DLDELIVERY.dbo.clients (id, name)
        VALUES (@client_id, @client_name);
    END

    -- Se existe um registro PENDENTE em 'routes'
    IF @route_id IS NOT NULL
    BEGIN
        -- Verifique se já existe um registro na tabela 'routes_clients' com o mesmo cliente e rota
        IF NOT EXISTS (SELECT 1 FROM DLDELIVERY.dbo.routes_clients WHERE routeid = @route_id AND clientid = @client_id)
        BEGIN
            -- Insira na tabela 'routes_clients'
            INSERT INTO DLDELIVERY.dbo.routes_clients (routeid, clientid, phonenumber)
            VALUES (@route_id, @client_id, @phonenumber);
        END
    END
    ELSE
    BEGIN
        -- Verifique se existe um usuário na tabela 'users' com o id @deliveryman
        IF EXISTS (SELECT 1 FROM DLDELIVERY.dbo.users WHERE id = @deliveryman)
        BEGIN
            -- Crie um novo registro na tabela 'routes'
            INSERT INTO DLDELIVERY.dbo.routes (deliveryman, [user])
            VALUES (@deliveryman, 1);

            -- Pegue o id da nova rota criada
            SET @route_id = SCOPE_IDENTITY();

            -- Verifique se já existe um registro na tabela 'routes_clients' com o mesmo cliente e rota
            IF NOT EXISTS (SELECT 1 FROM DLDELIVERY.dbo.routes_clients WHERE routeid = @route_id AND clientid = @client_id)
            BEGIN
                -- Insira na tabela 'routes_clients'
                INSERT INTO DLDELIVERY.dbo.routes_clients (routeid, clientid, phonenumber)
                VALUES (@route_id, @client_id, @phonenumber);
            END
        END
    END

    -- Volte ao banco INOVAFARMABANCO
    EXEC('USE INOVAFARMABANCO');
END
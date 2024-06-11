CREATE TRIGGER [dbo].[clienteDELDELIVERYUpdate] ON [dbo].[Cadastro] 
AFTER UPDATE
AS
BEGIN
	DECLARE @codCadastro int, @codCliente int, @nomeCliente varchar(60)

	-- Loop para lidar com múltiplas atualizações
	DECLARE cur CURSOR FOR 
	SELECT i.CodigoCadastro, i.Nome
	FROM inserted i

	OPEN cur

	FETCH NEXT FROM cur INTO @codCadastro, @nomeCliente

	WHILE @@FETCH_STATUS = 0
	BEGIN
		-- Obtém o CodigoCliente associado ao CodigoCadastro
		SET @codCliente = (SELECT CodigoCliente FROM Cliente WHERE CodigoCadastro = @codCadastro)
		
		-- Se @codCliente não for nulo, verifica a existência na tabela DLDELIVERY.dbo.clients e atualiza se existir
		IF @codCliente IS NOT NULL
		BEGIN
			IF EXISTS (SELECT 1 FROM DLDELIVERY.dbo.clients WHERE id = @codCliente)
			BEGIN
				UPDATE DLDELIVERY.dbo.clients
				SET name = @nomeCliente
				WHERE id = @codCliente
			END
		END

		FETCH NEXT FROM cur INTO @codCadastro, @nomeCliente
	END

	CLOSE cur
	DEALLOCATE cur
END
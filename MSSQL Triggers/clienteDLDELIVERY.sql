CREATE TRIGGER [dbo].[clienteDLDELIVERY] ON [dbo].[Cliente] AFTER INSERT
AS
BEGIN
	DECLARE
		@codCliente int,
		@nomeCliente varchar(60)

    SET @codCliente = (SELECT CodigoCliente FROM inserted)
	SET @nomeCliente = (SELECT Cadastro.Nome FROM Cadastro INNER JOIN Cliente ON Cadastro.CodigoCadastro = Cliente.CodigoCadastro WHERE Cliente.CodigoCliente = @codCliente)
	  
	INSERT INTO DLDELIVERY.dbo.clients  (id, name) VALUES (@codCliente, @nomeCliente)
END
USE GesconDatabase
GO

-- Deshabilitar Ad Hoc Distributed Queries
EXEC sp_configure 'Ad Hoc Distributed Queries', 0;
RECONFIGURE;
EXEC sp_configure 'show advanced options', 0;
RECONFIGURE;
GO
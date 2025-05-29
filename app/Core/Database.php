<?php

namespace App\Core;

use mysqli; // Importa a classe mysqli para o namespace atual

class Database {
    private static $host = 'localhost';      // Seu host
    private static $db_name = 'sistema_faturas'; // Seu banco de dados
    private static $username = 'root';       // Seu usuário
    private static $password = '';            // Sua senha
    private static $charset = 'utf8';        // Seu charset

    private static $instance = null; // Para armazenar a instância da conexão

    // O construtor é privado para prevenir a criação direta de instâncias
    private function __construct() {}

    // O método clone é privado para prevenir a clonagem da instância
    private function __clone() {}

    public static function getInstance(): mysqli {
        if (self::$instance === null) {
            // Cria a conexão mysqli
            self::$instance = new mysqli(self::$host, self::$username, self::$password, self::$db_name);

            // Verifica a conexão
            if (self::$instance->connect_error) {
                // Em um ambiente de produção, você não deveria exibir detalhes do erro.
                // Grave em um log e mostre uma mensagem genérica.
                error_log("Falha na conexão com o banco de dados (MySQLi): " . self::$instance->connect_error);
                die('Erro de conexão com o banco. Por favor, tente mais tarde.');
            }

            // Define o charset
            if (!self::$instance->set_charset(self::$charset)) {
                // Opcional: Logar se o charset não puder ser definido, mas não necessariamente morrer.
                error_log("Erro ao definir o charset para " . self::$charset . ": " . self::$instance->error);
            }
        }
        return self::$instance;
    }
}
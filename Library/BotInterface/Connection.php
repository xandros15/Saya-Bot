<?php


namespace Library\BotInterface;


interface Connection {

    /**
     * Establishs the connection to the server.
     */
    public function connect();

    /**
     * Disconnects from the server.
     *
     * @return boolean True if the connection was closed. False otherwise.
     */
    public function disconnect();

    /**
     * Interaction with the server.
     * For example, send commands or some other data to the server.
     *
     * @return boolean FALSE on error.
     */
    public function sendData($data);

    /**
     * Returns data from the server.
     *
     * @return string|boolean The data as string, or false if no data is available or an error occured.
     */
    public function getData();

    /**
     * Check wether the connection exists.
     *
     * @return boolean True if the connection exists. False otherwise.
     */
    public function isConnected();
}

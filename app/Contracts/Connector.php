<?php


namespace App\Contracts;


interface Connector
{
    /**
     * the connector unique identifier
     *
     * @return string
     */
    public function getConnectorIdentifier(): string;

    /**
     * @return OAuth|string authInstance|auth class
     */
    public function getAuthService();

    /**
     * @return Webhook|string webhookInstance|auth class
     */
    public function getWebhookService();

    /**
     * @return array array of syncInstance
     */
    public function getSyncService(): array;

    /**
     * @return array array of processorInstance
     */
    public function getProcessorServices(): array;

    /**
     * @return array array of mapper
     */
    public function mapper(): array;
}

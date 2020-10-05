<?php

namespace App\Contracts;

interface ServiceConnector extends Connector
{
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

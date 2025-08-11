<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatGptService
{
    private $client;
    private $openApiKey;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $param)
    {
        $this->client = $client;
        $this->openApiKey = $param->get('openai.api_key');
    }

    public function generateWords(string $theme) : array
    {
        $response = $this->client->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->openApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un assistant littéraire spécialisé en poésie.'],
                    ['role' => 'user', 'content' => "Génère 3 mots poétiques autour du thème : \"$theme\". Réponds sans commentaire, un mot par ligne. Pour être innovant il est impératif que tu ne gardes pas en mémoire les demandes précédentes sinon tu donnes des mots trop proches les uns des autres."],
                ],
                'temperature' => 0.8,
            ],
        ]);

        $data = $response->toArray();
        $text = $data['choices'][0]['message']['content'] ?? '';
        return array_filter(array_map('trim', explode("\n", $text)));
    }
}
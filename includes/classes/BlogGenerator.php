<?php
class BlogGenerator {
    private $db;
    private $apiKey;
    private $model;
    private $imageModel;
    private $pexelsKey;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->apiKey = get_setting('openrouter_api_key');
        $this->model = get_setting('deepseek_model', 'deepseek/deepseek-r1');
        $this->imageModel = get_setting('image_generation_model', 'pexels');
        $this->pexelsKey = get_setting('pexels_api_key');
    }

    public function generateArticle() {
        $trends = $this->getGoogleTrends();
        $webapps = $this->getPublishedWebapps();
        $prompt = $this->buildPrompt($trends, $webapps);
        $aiResponse = $this->callOpenRouter($prompt);

        if (!$aiResponse) {
            throw new Exception('Error al generar contenido con IA');
        }

        $article = $this->parseAIResponse($aiResponse);
        $featuredImage = $this->getPexelsPhoto($article);
        
        if ($featuredImage) {
            $article['featured_image_url'] = $featuredImage;
        }

        $articleId = $this->saveArticle($article, $prompt, $trends);

        return [
            'success' => true,
            'article_id' => $articleId,
            'title' => $article['title'],
            'featured_image_url' => $featuredImage ?? null
        ];
    }

    private function getGoogleTrends() {
        $baseTopics = [
            'IA para pequeñas empresas',
            'Inteligencia artificial en negocios',
            'Automatización con IA',
            'Chatbots para empresas',
            'Machine Learning para PYMEs',
            'Transformación digital',
            'IA en ventas',
            'IA en marketing',
            'IA en recursos humanos',
            'IA en atención al cliente',
            'Análisis de datos con IA',
            'Predicción de ventas con IA'
        ];

        $sectors = [
            'comercio minorista',
            'restaurantes',
            'servicios profesionales',
            'manufactura',
            'salud y bienestar',
            'educación',
            'construcción',
            'turismo',
            'agricultura',
            'transporte'
        ];

        return [
            'main_topic' => $baseTopics[array_rand($baseTopics)],
            'sector' => $sectors[array_rand($sectors)],
            'region' => 'Latinoamérica',
            'search_volume' => 'alto',
            'trend' => 'creciente'
        ];
    }

    private function getPublishedWebapps() {
        return $this->db->fetchAll("
            SELECT id, title, short_description, category
            FROM webapps
            WHERE status = 'published'
            ORDER BY is_featured DESC, created_at DESC
            LIMIT 5
        ");
    }

    private function buildPrompt($trends, $webapps) {
        $webappsText = '';
        if (!empty($webapps)) {
            $webappsText = "\n\nAplicaciones disponibles en Guarani App Store:\n";
            foreach ($webapps as $webapp) {
                $webappsText .= "- {$webapp['title']}: {$webapp['short_description']}\n";
            }
        }

        return "Eres César Ruzafa, un experto reconocido en IA aplicada a PYMEs en Latinoamérica.
Escribe un artículo de blog profesional, educativo y práctico sobre: {$trends['main_topic']} aplicado al sector {$trends['sector']}.

IMPORTANTE:
- NO menciones que este contenido fue generado por IA
- Escribe en primera persona como César Ruzafa
- Usa un tono profesional pero cercano
- Incluye ejemplos prácticos y casos de uso reales
- El artículo debe tener entre 1200-1800 palabras
- Usa formato HTML para los párrafos y estructura
- INCLUYE 1-2 frases breves en guaraní (idioma cooficial de Paraguay) de manera natural y contextual, con su traducción al español entre paréntesis. Ejemplo: 'Mba'éichapa reñembokatupyrýta ne negocio' (¿Cómo vas a mejorar tu negocio?)
{$webappsText}

El artículo debe incluir:
1. Un título atractivo y SEO-friendly
2. Una introducción que enganche al lector
3. 3-4 secciones principales con subtítulos
4. Ejemplos concretos y aplicables
5. Una conclusión con llamado a la acción
6. Un meta description de máximo 160 caracteres

Formato de respuesta (JSON):
{
    \"title\": \"Título del artículo\",
    \"excerpt\": \"Resumen breve del artículo (150-200 caracteres)\",
    \"content\": \"Contenido completo en HTML\",
    \"seo_title\": \"Título optimizado para SEO\",
    \"seo_description\": \"Meta description para SEO\",
    \"tags\": [\"tag1\", \"tag2\", \"tag3\"]
}";
    }

    private function callOpenRouter($prompt) {
        if (empty($this->apiKey)) {
            throw new Exception('API Key de OpenRouter no configurada');
        }

        $ch = curl_init(OPENROUTER_API_URL);

        $data = [
            'model' => $this->model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.7,
            'max_tokens' => 4000
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
                'HTTP-Referer: ' . SITE_URL,
                'X-Title: Guarani App Store Blog'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode !== 200) {
            log_error("OpenRouter API error: " . ($error ?: "HTTP $httpCode"));
            return false;
        }

        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? false;
    }

    private function parseAIResponse($response) {
        $jsonMatch = [];
        if (preg_match('/\{[\s\S]*\}/', $response, $jsonMatch)) {
            $parsed = json_decode($jsonMatch[0], true);
            if ($parsed) return $parsed;
        }

        return [
            'title' => 'La IA está Transformando las PYMEs en Latinoamérica',
            'excerpt' => 'Descubre cómo la inteligencia artificial puede revolucionar tu negocio',
            'content' => $response,
            'seo_title' => 'IA para PYMEs: Guía Completa 2025',
            'seo_description' => 'Aprende cómo implementar IA en tu PYME. Casos prácticos, herramientas y estrategias.',
            'tags' => ['IA', 'PYMEs', 'Transformación Digital']
        ];
    }

    private function saveArticle($article, $prompt, $trends) {
        $slug = generate_slug($article['title']);

        $existingSlug = $this->db->fetchOne("SELECT id FROM blog_articles WHERE slug = ?", [$slug]);
        if ($existingSlug) {
            $slug .= '-' . time();
        }

        $relatedWebappId = null;
        if (!empty($article['content'])) {
            $webapps = $this->getPublishedWebapps();
            foreach ($webapps as $webapp) {
                if (stripos($article['content'], $webapp['title']) !== false) {
                    $relatedWebappId = $webapp['id'];
                    break;
                }
            }
        }

        return $this->db->insert('blog_articles', [
            'title' => $article['title'],
            'slug' => $slug,
            'excerpt' => $article['excerpt'] ?? truncate_text(strip_tags($article['content']), 200),
            'content' => $article['content'],
            'author_name' => get_setting('blog_author_name', 'César Ruzafa'),
            'category' => $trends['sector'] ?? 'Transformación Digital',
            'tags' => json_encode($article['tags'] ?? ['IA', 'PYMEs']),
            'related_webapp_id' => $relatedWebappId,
            'status' => 'draft',
            'seo_title' => $article['seo_title'] ?? $article['title'],
            'seo_description' => $article['seo_description'] ?? $article['excerpt'],
            'generation_prompt' => $prompt,
            'google_trends_data' => json_encode($trends),
            'is_auto_generated' => 1,
            'featured_image_url' => $article['featured_image_url'] ?? null,
            'published_at' => null
        ]);
    }

    public function notifySubscribers($articleId) {
        $article = $this->db->fetchOne("SELECT * FROM blog_articles WHERE id = ?", [$articleId]);
        if (!$article) return false;

        $subscribers = $this->db->fetchAll("SELECT email FROM blog_subscribers WHERE status = 'active'");
        foreach ($subscribers as $subscriber) {
            // TODO: Implementar envío real de email
        }
        return true;
    }

    /**
     * Obtener imagen profesional de Pexels API
     */
    private function getPexelsPhoto($article) {
        if (empty($this->pexelsKey)) {
            log_error('Pexels API Key no configurada');
            return null;
        }

        try {
            $keywords = $this->extractKeywords($article['title']);
            $query = urlencode($keywords);
            
            // Pexels API endpoint
            $url = "https://api.pexels.com/v1/search?query={$query}&per_page=1&orientation=landscape";

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $this->pexelsKey
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error || $httpCode !== 200) {
                log_error("Pexels API error. HTTP: $httpCode, Error: $error");
                return null;
            }

            $result = json_decode($response, true);

            if (!isset($result['photos'][0]['src']['large2x'])) {
                log_error("Pexels: No se encontraron fotos para: $keywords");
                return null;
            }

            $imageUrl = $result['photos'][0]['src']['large2x'];

            // Descargar imagen
            $ch = curl_init($imageUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $imageData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || empty($imageData)) {
                log_error("No se pudo descargar imagen de Pexels");
                return null;
            }

            // Guardar localmente
            $uploadDir = PUBLIC_PATH . '/assets/images/blog';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = generate_slug($article['title']) . '-' . time() . '.jpg';
            $filepath = $uploadDir . '/' . $filename;

            if (file_put_contents($filepath, $imageData) === false) {
                log_error("No se pudo guardar imagen localmente");
                return null;
            }

            return ASSETS_URL . '/images/blog/' . $filename;

        } catch (Exception $e) {
            log_error("Error obteniendo imagen de Pexels: " . $e->getMessage());
            return null;
        }
    }

    private function extractKeywords($title) {
        $keywords = 'business technology';
        
        if (stripos($title, 'restaurante') !== false) $keywords = 'restaurant business';
        if (stripos($title, 'salud') !== false) $keywords = 'healthcare technology';
        if (stripos($title, 'educación') !== false) $keywords = 'education technology';
        if (stripos($title, 'retail') !== false || stripos($title, 'comercio') !== false) $keywords = 'retail business';
        if (stripos($title, 'IA') !== false || stripos($title, 'inteligencia') !== false) $keywords = 'artificial intelligence technology';
        
        return $keywords;
    }
}

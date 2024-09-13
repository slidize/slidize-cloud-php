<?php              

/**
 * --------------------------------------------------------------------------------------------------------------------
 * <copyright company="Smallize">
 *   Copyright (c) 2024 Slidize for Cloud
 * </copyright>
 * <summary>
 *   Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 * 
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 * 
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *  SOFTWARE.
 * </summary>
 * --------------------------------------------------------------------------------------------------------------------
 */


namespace Slidize\Cloud\Sdk\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Slidize\Cloud\Sdk\ApiException;
use Slidize\Cloud\Sdk\Configuration;
use Slidize\Cloud\Sdk\HeaderSelector;
use Slidize\Cloud\Sdk\ObjectSerializer;

class SlidizeApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /** @var string[] $contentTypes **/
    public const contentTypes = [
        'convert' => [
            'multipart/form-data',
        ],
        'convertToVideo' => [
            'multipart/form-data',
        ],
        'imageWatermark' => [
            'multipart/form-data',
        ],
        'merge' => [
            'multipart/form-data',
        ],
        'protect' => [
            'multipart/form-data',
        ],
        'removeAnnotations' => [
            'multipart/form-data',
        ],
        'removeMacros' => [
            'multipart/form-data',
        ],
        'replaceText' => [
            'multipart/form-data',
        ],
        'split' => [
            'multipart/form-data',
        ],
        'textWatermark' => [
            'multipart/form-data',
        ],
        'unprotect' => [
            'multipart/form-data',
        ],
    ];

/**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     * @param int             $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null,
        $hostIndex = 0
    ) {
        $this->client = $client ?: new Client([ 'verify' => false ]);
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex($hostIndex): void
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex()
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation convert
     *
     * Converts files provided in the request body into target format and returns conversion result.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject[] $documents documents (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['convert'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function convert($format, $documents, string $contentType = self::contentTypes['convert'][0])
    {
        list($response) = $this->convertWithHttpInfo($format, $documents, $contentType);
        return $response;
    }

    /**
     * Operation convertWithHttpInfo
     *
     * Converts files provided in the request body into target format and returns conversion result.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject[] $documents (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['convert'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function convertWithHttpInfo($format, $documents, string $contentType = self::contentTypes['convert'][0])
    {
        $request = $this->convertRequest($format, $documents, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\SplFileObject' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\SplFileObject' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\SplFileObject', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\SplFileObject';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplFileObject',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation convertAsync
     *
     * Converts files provided in the request body into target format and returns conversion result.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject[] $documents (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['convert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function convertAsync($format, $documents, string $contentType = self::contentTypes['convert'][0])
    {
        return $this->convertAsyncWithHttpInfo($format, $documents, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation convertAsyncWithHttpInfo
     *
     * Converts files provided in the request body into target format and returns conversion result.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject[] $documents (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['convert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function convertAsyncWithHttpInfo($format, $documents, string $contentType = self::contentTypes['convert'][0])
    {
        $returnType = '\SplFileObject';
        $request = $this->convertRequest($format, $documents, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'convert'
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject[] $documents (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['convert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function convertRequest($format, $documents, string $contentType = self::contentTypes['convert'][0])
    {

        // verify the required parameter 'format' is set
        if ($format === null || (is_array($format) && count($format) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $format when calling convert'
            );
        }

        // verify the required parameter 'documents' is set
        if ($documents === null || (is_array($documents) && count($documents) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $documents when calling convert'
            );
        }


        $resourcePath = '/convert/{format}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($format !== null) {
            $resourcePath = str_replace(
                '{' . 'format' . '}',
                ObjectSerializer::toPathValue($format),
                $resourcePath
            );
        }

        // form params
        if ($documents !== null) {
            $multipart = true;
            $formParams['documents'] = [];
            $paramFiles = is_array($documents) ? $documents : [$documents];
            foreach ($paramFiles as $paramFile) {
                $formParams['documents'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }

        $headers = $this->headerSelector->selectHeaders(
            ['text/plain', 'application/json', 'text/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation convertToVideo
     *
     * Converts file provided in the request body into video.
     *
     * @param  \SplFileObject $document document (required)
     * @param  \Slidize\Cloud\Sdk\Models\VideoOptions $options options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['convertToVideo'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function convertToVideo($document, $options = null, string $contentType = self::contentTypes['convertToVideo'][0])
    {
        list($response) = $this->convertToVideoWithHttpInfo($document, $options, $contentType);
        return $response;
    }

    /**
     * Operation convertToVideoWithHttpInfo
     *
     * Converts file provided in the request body into video.
     *
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\VideoOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['convertToVideo'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function convertToVideoWithHttpInfo($document, $options = null, string $contentType = self::contentTypes['convertToVideo'][0])
    {
        $request = $this->convertToVideoRequest($document, $options, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\SplFileObject' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\SplFileObject' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\SplFileObject', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\SplFileObject';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplFileObject',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation convertToVideoAsync
     *
     * Converts file provided in the request body into video.
     *
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\VideoOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['convertToVideo'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function convertToVideoAsync($document, $options = null, string $contentType = self::contentTypes['convertToVideo'][0])
    {
        return $this->convertToVideoAsyncWithHttpInfo($document, $options, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation convertToVideoAsyncWithHttpInfo
     *
     * Converts file provided in the request body into video.
     *
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\VideoOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['convertToVideo'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function convertToVideoAsyncWithHttpInfo($document, $options = null, string $contentType = self::contentTypes['convertToVideo'][0])
    {
        $returnType = '\SplFileObject';
        $request = $this->convertToVideoRequest($document, $options, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'convertToVideo'
     *
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\VideoOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['convertToVideo'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function convertToVideoRequest($document, $options = null, string $contentType = self::contentTypes['convertToVideo'][0])
    {

        // verify the required parameter 'document' is set
        if ($document === null || (is_array($document) && count($document) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $document when calling convertToVideo'
            );
        }



        $resourcePath = '/video';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;




        // form params
        if ($document !== null) {
            $multipart = true;
            $formParams['document'] = [];
            $paramFiles = is_array($document) ? $document : [$document];
            foreach ($paramFiles as $paramFile) {
                $formParams['document'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }
        // form params
        if ($options !== null) {
            $formParams['options'] = ObjectSerializer::toFormValue($options);
        }

        $headers = $this->headerSelector->selectHeaders(
            ['text/plain', 'application/json', 'text/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation imageWatermark
     *
     * Adds image watermarks and return result.
     *
     * @param  \SplFileObject[] $documents documents (required)
     * @param  \SplFileObject $image image (required)
     * @param  \Slidize\Cloud\Sdk\Models\ImageWatermarkOptions $options options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['imageWatermark'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function imageWatermark($documents, $image, $options = null, string $contentType = self::contentTypes['imageWatermark'][0])
    {
        list($response) = $this->imageWatermarkWithHttpInfo($documents, $image, $options, $contentType);
        return $response;
    }

    /**
     * Operation imageWatermarkWithHttpInfo
     *
     * Adds image watermarks and return result.
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \SplFileObject $image (required)
     * @param  \Slidize\Cloud\Sdk\Models\ImageWatermarkOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['imageWatermark'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function imageWatermarkWithHttpInfo($documents, $image, $options = null, string $contentType = self::contentTypes['imageWatermark'][0])
    {
        $request = $this->imageWatermarkRequest($documents, $image, $options, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\SplFileObject' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\SplFileObject' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\SplFileObject', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\SplFileObject';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplFileObject',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation imageWatermarkAsync
     *
     * Adds image watermarks and return result.
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \SplFileObject $image (required)
     * @param  \Slidize\Cloud\Sdk\Models\ImageWatermarkOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['imageWatermark'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function imageWatermarkAsync($documents, $image, $options = null, string $contentType = self::contentTypes['imageWatermark'][0])
    {
        return $this->imageWatermarkAsyncWithHttpInfo($documents, $image, $options, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation imageWatermarkAsyncWithHttpInfo
     *
     * Adds image watermarks and return result.
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \SplFileObject $image (required)
     * @param  \Slidize\Cloud\Sdk\Models\ImageWatermarkOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['imageWatermark'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function imageWatermarkAsyncWithHttpInfo($documents, $image, $options = null, string $contentType = self::contentTypes['imageWatermark'][0])
    {
        $returnType = '\SplFileObject';
        $request = $this->imageWatermarkRequest($documents, $image, $options, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'imageWatermark'
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \SplFileObject $image (required)
     * @param  \Slidize\Cloud\Sdk\Models\ImageWatermarkOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['imageWatermark'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function imageWatermarkRequest($documents, $image, $options = null, string $contentType = self::contentTypes['imageWatermark'][0])
    {

        // verify the required parameter 'documents' is set
        if ($documents === null || (is_array($documents) && count($documents) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $documents when calling imageWatermark'
            );
        }

        // verify the required parameter 'image' is set
        if ($image === null || (is_array($image) && count($image) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $image when calling imageWatermark'
            );
        }



        $resourcePath = '/watermark/image';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;




        // form params
        if ($documents !== null) {
            $multipart = true;
            $formParams['documents'] = [];
            $paramFiles = is_array($documents) ? $documents : [$documents];
            foreach ($paramFiles as $paramFile) {
                $formParams['documents'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }
        // form params
        if ($image !== null) {
            $multipart = true;
            $formParams['image'] = [];
            $paramFiles = is_array($image) ? $image : [$image];
            foreach ($paramFiles as $paramFile) {
                $formParams['image'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }
        // form params
        if ($options !== null) {
            $formParams['options'] = ObjectSerializer::toFormValue($options);
        }

        $headers = $this->headerSelector->selectHeaders(
            ['text/plain', 'application/json', 'text/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation merge
     *
     * Merges files provided in the request and saves the merge result into target format.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject[] $documents documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\MergeOptions $options options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['merge'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function merge($format, $documents, $options = null, string $contentType = self::contentTypes['merge'][0])
    {
        list($response) = $this->mergeWithHttpInfo($format, $documents, $options, $contentType);
        return $response;
    }

    /**
     * Operation mergeWithHttpInfo
     *
     * Merges files provided in the request and saves the merge result into target format.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\MergeOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['merge'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function mergeWithHttpInfo($format, $documents, $options = null, string $contentType = self::contentTypes['merge'][0])
    {
        $request = $this->mergeRequest($format, $documents, $options, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\SplFileObject' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\SplFileObject' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\SplFileObject', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\SplFileObject';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplFileObject',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation mergeAsync
     *
     * Merges files provided in the request and saves the merge result into target format.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\MergeOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['merge'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function mergeAsync($format, $documents, $options = null, string $contentType = self::contentTypes['merge'][0])
    {
        return $this->mergeAsyncWithHttpInfo($format, $documents, $options, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation mergeAsyncWithHttpInfo
     *
     * Merges files provided in the request and saves the merge result into target format.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\MergeOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['merge'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function mergeAsyncWithHttpInfo($format, $documents, $options = null, string $contentType = self::contentTypes['merge'][0])
    {
        $returnType = '\SplFileObject';
        $request = $this->mergeRequest($format, $documents, $options, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'merge'
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\MergeOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['merge'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function mergeRequest($format, $documents, $options = null, string $contentType = self::contentTypes['merge'][0])
    {

        // verify the required parameter 'format' is set
        if ($format === null || (is_array($format) && count($format) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $format when calling merge'
            );
        }

        // verify the required parameter 'documents' is set
        if ($documents === null || (is_array($documents) && count($documents) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $documents when calling merge'
            );
        }



        $resourcePath = '/merge/{format}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($format !== null) {
            $resourcePath = str_replace(
                '{' . 'format' . '}',
                ObjectSerializer::toPathValue($format),
                $resourcePath
            );
        }

        // form params
        if ($documents !== null) {
            $multipart = true;
            $formParams['documents'] = [];
            $paramFiles = is_array($documents) ? $documents : [$documents];
            foreach ($paramFiles as $paramFile) {
                $formParams['documents'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }
        // form params
        if ($options !== null) {
            $formParams['options'] = ObjectSerializer::toFormValue($options);
        }

        $headers = $this->headerSelector->selectHeaders(
            ['text/plain', 'application/json', 'text/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation protect
     *
     * Protects presentation with specified password and returns result.
     *
     * @param  \SplFileObject $document document (required)
     * @param  \Slidize\Cloud\Sdk\Models\ProtectionOptions $options options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['protect'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function protect($document, $options = null, string $contentType = self::contentTypes['protect'][0])
    {
        list($response) = $this->protectWithHttpInfo($document, $options, $contentType);
        return $response;
    }

    /**
     * Operation protectWithHttpInfo
     *
     * Protects presentation with specified password and returns result.
     *
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\ProtectionOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['protect'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function protectWithHttpInfo($document, $options = null, string $contentType = self::contentTypes['protect'][0])
    {
        $request = $this->protectRequest($document, $options, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\SplFileObject' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\SplFileObject' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\SplFileObject', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\SplFileObject';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplFileObject',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation protectAsync
     *
     * Protects presentation with specified password and returns result.
     *
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\ProtectionOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['protect'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function protectAsync($document, $options = null, string $contentType = self::contentTypes['protect'][0])
    {
        return $this->protectAsyncWithHttpInfo($document, $options, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation protectAsyncWithHttpInfo
     *
     * Protects presentation with specified password and returns result.
     *
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\ProtectionOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['protect'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function protectAsyncWithHttpInfo($document, $options = null, string $contentType = self::contentTypes['protect'][0])
    {
        $returnType = '\SplFileObject';
        $request = $this->protectRequest($document, $options, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'protect'
     *
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\ProtectionOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['protect'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function protectRequest($document, $options = null, string $contentType = self::contentTypes['protect'][0])
    {

        // verify the required parameter 'document' is set
        if ($document === null || (is_array($document) && count($document) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $document when calling protect'
            );
        }



        $resourcePath = '/lock';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;




        // form params
        if ($document !== null) {
            $multipart = true;
            $formParams['document'] = [];
            $paramFiles = is_array($document) ? $document : [$document];
            foreach ($paramFiles as $paramFile) {
                $formParams['document'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }
        // form params
        if ($options !== null) {
            $formParams['options'] = ObjectSerializer::toFormValue($options);
        }

        $headers = $this->headerSelector->selectHeaders(
            ['text/plain', 'application/json', 'text/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation removeAnnotations
     *
     * Remove annotations from presentation
     *
     * @param  \SplFileObject $document document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['removeAnnotations'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function removeAnnotations($document, string $contentType = self::contentTypes['removeAnnotations'][0])
    {
        list($response) = $this->removeAnnotationsWithHttpInfo($document, $contentType);
        return $response;
    }

    /**
     * Operation removeAnnotationsWithHttpInfo
     *
     * Remove annotations from presentation
     *
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['removeAnnotations'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function removeAnnotationsWithHttpInfo($document, string $contentType = self::contentTypes['removeAnnotations'][0])
    {
        $request = $this->removeAnnotationsRequest($document, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\SplFileObject' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\SplFileObject' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\SplFileObject', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\SplFileObject';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplFileObject',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation removeAnnotationsAsync
     *
     * Remove annotations from presentation
     *
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['removeAnnotations'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function removeAnnotationsAsync($document, string $contentType = self::contentTypes['removeAnnotations'][0])
    {
        return $this->removeAnnotationsAsyncWithHttpInfo($document, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation removeAnnotationsAsyncWithHttpInfo
     *
     * Remove annotations from presentation
     *
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['removeAnnotations'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function removeAnnotationsAsyncWithHttpInfo($document, string $contentType = self::contentTypes['removeAnnotations'][0])
    {
        $returnType = '\SplFileObject';
        $request = $this->removeAnnotationsRequest($document, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'removeAnnotations'
     *
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['removeAnnotations'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function removeAnnotationsRequest($document, string $contentType = self::contentTypes['removeAnnotations'][0])
    {

        // verify the required parameter 'document' is set
        if ($document === null || (is_array($document) && count($document) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $document when calling removeAnnotations'
            );
        }


        $resourcePath = '/removeAnnotations';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;




        // form params
        if ($document !== null) {
            $multipart = true;
            $formParams['document'] = [];
            $paramFiles = is_array($document) ? $document : [$document];
            foreach ($paramFiles as $paramFile) {
                $formParams['document'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }

        $headers = $this->headerSelector->selectHeaders(
            ['text/plain', 'application/json', 'text/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation removeMacros
     *
     * Remove macros from presentation
     *
     * @param  \SplFileObject $document document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['removeMacros'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function removeMacros($document, string $contentType = self::contentTypes['removeMacros'][0])
    {
        list($response) = $this->removeMacrosWithHttpInfo($document, $contentType);
        return $response;
    }

    /**
     * Operation removeMacrosWithHttpInfo
     *
     * Remove macros from presentation
     *
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['removeMacros'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function removeMacrosWithHttpInfo($document, string $contentType = self::contentTypes['removeMacros'][0])
    {
        $request = $this->removeMacrosRequest($document, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\SplFileObject' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\SplFileObject' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\SplFileObject', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\SplFileObject';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplFileObject',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation removeMacrosAsync
     *
     * Remove macros from presentation
     *
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['removeMacros'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function removeMacrosAsync($document, string $contentType = self::contentTypes['removeMacros'][0])
    {
        return $this->removeMacrosAsyncWithHttpInfo($document, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation removeMacrosAsyncWithHttpInfo
     *
     * Remove macros from presentation
     *
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['removeMacros'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function removeMacrosAsyncWithHttpInfo($document, string $contentType = self::contentTypes['removeMacros'][0])
    {
        $returnType = '\SplFileObject';
        $request = $this->removeMacrosRequest($document, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'removeMacros'
     *
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['removeMacros'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function removeMacrosRequest($document, string $contentType = self::contentTypes['removeMacros'][0])
    {

        // verify the required parameter 'document' is set
        if ($document === null || (is_array($document) && count($document) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $document when calling removeMacros'
            );
        }


        $resourcePath = '/removeMacros';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;




        // form params
        if ($document !== null) {
            $multipart = true;
            $formParams['document'] = [];
            $paramFiles = is_array($document) ? $document : [$document];
            foreach ($paramFiles as $paramFile) {
                $formParams['document'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }

        $headers = $this->headerSelector->selectHeaders(
            ['text/plain', 'application/json', 'text/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation replaceText
     *
     * Replace text in presentation.
     *
     * @param  \SplFileObject[] $documents documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\ReplaceTextOptions $options options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['replaceText'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function replaceText($documents, $options = null, string $contentType = self::contentTypes['replaceText'][0])
    {
        list($response) = $this->replaceTextWithHttpInfo($documents, $options, $contentType);
        return $response;
    }

    /**
     * Operation replaceTextWithHttpInfo
     *
     * Replace text in presentation.
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\ReplaceTextOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['replaceText'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function replaceTextWithHttpInfo($documents, $options = null, string $contentType = self::contentTypes['replaceText'][0])
    {
        $request = $this->replaceTextRequest($documents, $options, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\SplFileObject' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\SplFileObject' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\SplFileObject', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\SplFileObject';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplFileObject',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation replaceTextAsync
     *
     * Replace text in presentation.
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\ReplaceTextOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['replaceText'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function replaceTextAsync($documents, $options = null, string $contentType = self::contentTypes['replaceText'][0])
    {
        return $this->replaceTextAsyncWithHttpInfo($documents, $options, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation replaceTextAsyncWithHttpInfo
     *
     * Replace text in presentation.
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\ReplaceTextOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['replaceText'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function replaceTextAsyncWithHttpInfo($documents, $options = null, string $contentType = self::contentTypes['replaceText'][0])
    {
        $returnType = '\SplFileObject';
        $request = $this->replaceTextRequest($documents, $options, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'replaceText'
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\ReplaceTextOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['replaceText'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function replaceTextRequest($documents, $options = null, string $contentType = self::contentTypes['replaceText'][0])
    {

        // verify the required parameter 'documents' is set
        if ($documents === null || (is_array($documents) && count($documents) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $documents when calling replaceText'
            );
        }



        $resourcePath = '/replaceText';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;




        // form params
        if ($documents !== null) {
            $multipart = true;
            $formParams['documents'] = [];
            $paramFiles = is_array($documents) ? $documents : [$documents];
            foreach ($paramFiles as $paramFile) {
                $formParams['documents'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }
        // form params
        if ($options !== null) {
            $formParams['options'] = ObjectSerializer::toFormValue($options);
        }

        $headers = $this->headerSelector->selectHeaders(
            ['text/plain', 'application/json', 'text/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation split
     *
     * Splits presentation according to the specified slides range and saves result into target format.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject $document document (required)
     * @param  \Slidize\Cloud\Sdk\Models\SplitOptions $options options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['split'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function split($format, $document, $options = null, string $contentType = self::contentTypes['split'][0])
    {
        list($response) = $this->splitWithHttpInfo($format, $document, $options, $contentType);
        return $response;
    }

    /**
     * Operation splitWithHttpInfo
     *
     * Splits presentation according to the specified slides range and saves result into target format.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\SplitOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['split'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function splitWithHttpInfo($format, $document, $options = null, string $contentType = self::contentTypes['split'][0])
    {
        $request = $this->splitRequest($format, $document, $options, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\SplFileObject' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\SplFileObject' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\SplFileObject', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\SplFileObject';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplFileObject',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation splitAsync
     *
     * Splits presentation according to the specified slides range and saves result into target format.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\SplitOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['split'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function splitAsync($format, $document, $options = null, string $contentType = self::contentTypes['split'][0])
    {
        return $this->splitAsyncWithHttpInfo($format, $document, $options, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation splitAsyncWithHttpInfo
     *
     * Splits presentation according to the specified slides range and saves result into target format.
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\SplitOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['split'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function splitAsyncWithHttpInfo($format, $document, $options = null, string $contentType = self::contentTypes['split'][0])
    {
        $returnType = '\SplFileObject';
        $request = $this->splitRequest($format, $document, $options, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'split'
     *
     * @param  ExportFormat $format Output file format. (required)
     * @param  \SplFileObject $document (required)
     * @param  \Slidize\Cloud\Sdk\Models\SplitOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['split'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function splitRequest($format, $document, $options = null, string $contentType = self::contentTypes['split'][0])
    {

        // verify the required parameter 'format' is set
        if ($format === null || (is_array($format) && count($format) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $format when calling split'
            );
        }

        // verify the required parameter 'document' is set
        if ($document === null || (is_array($document) && count($document) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $document when calling split'
            );
        }



        $resourcePath = '/split/{format}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($format !== null) {
            $resourcePath = str_replace(
                '{' . 'format' . '}',
                ObjectSerializer::toPathValue($format),
                $resourcePath
            );
        }

        // form params
        if ($document !== null) {
            $multipart = true;
            $formParams['document'] = [];
            $paramFiles = is_array($document) ? $document : [$document];
            foreach ($paramFiles as $paramFile) {
                $formParams['document'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }
        // form params
        if ($options !== null) {
            $formParams['options'] = ObjectSerializer::toFormValue($options);
        }

        $headers = $this->headerSelector->selectHeaders(
            ['text/plain', 'application/json', 'text/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation textWatermark
     *
     * Adds text watermarks and return result.
     *
     * @param  \SplFileObject[] $documents documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\TextWatermarkOptions $options options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['textWatermark'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function textWatermark($documents, $options = null, string $contentType = self::contentTypes['textWatermark'][0])
    {
        list($response) = $this->textWatermarkWithHttpInfo($documents, $options, $contentType);
        return $response;
    }

    /**
     * Operation textWatermarkWithHttpInfo
     *
     * Adds text watermarks and return result.
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\TextWatermarkOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['textWatermark'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function textWatermarkWithHttpInfo($documents, $options = null, string $contentType = self::contentTypes['textWatermark'][0])
    {
        $request = $this->textWatermarkRequest($documents, $options, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\SplFileObject' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\SplFileObject' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\SplFileObject', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\SplFileObject';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplFileObject',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation textWatermarkAsync
     *
     * Adds text watermarks and return result.
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\TextWatermarkOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['textWatermark'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function textWatermarkAsync($documents, $options = null, string $contentType = self::contentTypes['textWatermark'][0])
    {
        return $this->textWatermarkAsyncWithHttpInfo($documents, $options, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation textWatermarkAsyncWithHttpInfo
     *
     * Adds text watermarks and return result.
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\TextWatermarkOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['textWatermark'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function textWatermarkAsyncWithHttpInfo($documents, $options = null, string $contentType = self::contentTypes['textWatermark'][0])
    {
        $returnType = '\SplFileObject';
        $request = $this->textWatermarkRequest($documents, $options, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'textWatermark'
     *
     * @param  \SplFileObject[] $documents (required)
     * @param  \Slidize\Cloud\Sdk\Models\TextWatermarkOptions $options (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['textWatermark'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function textWatermarkRequest($documents, $options = null, string $contentType = self::contentTypes['textWatermark'][0])
    {

        // verify the required parameter 'documents' is set
        if ($documents === null || (is_array($documents) && count($documents) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $documents when calling textWatermark'
            );
        }



        $resourcePath = '/watermark/text';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;




        // form params
        if ($documents !== null) {
            $multipart = true;
            $formParams['documents'] = [];
            $paramFiles = is_array($documents) ? $documents : [$documents];
            foreach ($paramFiles as $paramFile) {
                $formParams['documents'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }
        // form params
        if ($options !== null) {
            $formParams['options'] = ObjectSerializer::toFormValue($options);
        }

        $headers = $this->headerSelector->selectHeaders(
            ['text/plain', 'application/json', 'text/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation unprotect
     *
     * Removes password from the presentation and returns result.
     *
     * @param  string $password Password to remove. (required)
     * @param  \SplFileObject $document document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['unprotect'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function unprotect($password, $document, string $contentType = self::contentTypes['unprotect'][0])
    {
        list($response) = $this->unprotectWithHttpInfo($password, $document, $contentType);
        return $response;
    }

    /**
     * Operation unprotectWithHttpInfo
     *
     * Removes password from the presentation and returns result.
     *
     * @param  string $password Password to remove. (required)
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['unprotect'] to see the possible values for this operation
     *
     * @throws \Slidize\Cloud\Sdk\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function unprotectWithHttpInfo($password, $document, string $contentType = self::contentTypes['unprotect'][0])
    {
        $request = $this->unprotectRequest($password, $document, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\SplFileObject' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\SplFileObject' !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\SplFileObject', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\SplFileObject';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplFileObject',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation unprotectAsync
     *
     * Removes password from the presentation and returns result.
     *
     * @param  string $password Password to remove. (required)
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['unprotect'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function unprotectAsync($password, $document, string $contentType = self::contentTypes['unprotect'][0])
    {
        return $this->unprotectAsyncWithHttpInfo($password, $document, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation unprotectAsyncWithHttpInfo
     *
     * Removes password from the presentation and returns result.
     *
     * @param  string $password Password to remove. (required)
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['unprotect'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function unprotectAsyncWithHttpInfo($password, $document, string $contentType = self::contentTypes['unprotect'][0])
    {
        $returnType = '\SplFileObject';
        $request = $this->unprotectRequest($password, $document, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'unprotect'
     *
     * @param  string $password Password to remove. (required)
     * @param  \SplFileObject $document (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['unprotect'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function unprotectRequest($password, $document, string $contentType = self::contentTypes['unprotect'][0])
    {

        // verify the required parameter 'password' is set
        if ($password === null || (is_array($password) && count($password) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $password when calling unprotect'
            );
        }

        // verify the required parameter 'document' is set
        if ($document === null || (is_array($document) && count($document) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $document when calling unprotect'
            );
        }


        $resourcePath = '/unlock';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($password !== null) {
            $headerParams['password'] = ObjectSerializer::toHeaderValue($password);
        }


        // form params
        if ($document !== null) {
            $multipart = true;
            $formParams['document'] = [];
            $paramFiles = is_array($document) ? $document : [$document];
            foreach ($paramFiles as $paramFile) {
                $formParams['document'][] = \GuzzleHttp\Psr7\Utils::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }

        $headers = $this->headerSelector->selectHeaders(
            ['text/plain', 'application/json', 'text/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}

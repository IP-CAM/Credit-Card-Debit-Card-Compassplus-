<?php
namespace GuzzleHttp; use GuzzleHttp\Message\MessageFactoryInterface; use GuzzleHttp\Message\RequestInterface; use GuzzleHttp\Event\ProgressEvent; use GuzzleHttp\Message\Request; use GuzzleHttp\Ring\Core; use GuzzleHttp\Stream\Stream; use GuzzleHttp\Exception\RequestException; class RingBridge { public static function createRingRequest(RequestInterface $request) { $options = $request->getConfig()->toArray(); $url = $request->getUrl(); $qs = ($pos = strpos($url, '?')) ? substr($url, $pos + 1) : null; return [ 'scheme' => $request->getScheme(), 'http_method' => $request->getMethod(), 'url' => $url, 'uri' => $request->getPath(), 'headers' => $request->getHeaders(), 'body' => $request->getBody(), 'version' => $request->getProtocolVersion(), 'client' => $options, 'query_string' => $qs, 'future' => isset($options['future']) ? $options['future'] : false ]; } public static function prepareRingRequest(Transaction $trans) { $trans->exception = null; $request = self::createRingRequest($trans->request); if ($trans->request->getEmitter()->hasListeners('progress')) { $emitter = $trans->request->getEmitter(); $request['client']['progress'] = function ($a, $b, $c, $d) use ($trans, $emitter) { $emitter->emit('progress', new ProgressEvent($trans, $a, $b, $c, $d)); }; } return $request; } public static function completeRingResponse( Transaction $trans, array $response, MessageFactoryInterface $messageFactory ) { $trans->state = 'complete'; $trans->transferInfo = isset($response['transfer_stats']) ? $response['transfer_stats'] : []; if (!empty($response['status'])) { $options = []; if (isset($response['version'])) { $options['protocol_version'] = $response['version']; } if (isset($response['reason'])) { $options['reason_phrase'] = $response['reason']; } $trans->response = $messageFactory->createResponse( $response['status'], isset($response['headers']) ? $response['headers'] : [], isset($response['body']) ? $response['body'] : null, $options ); if (isset($response['effective_url'])) { $trans->response->setEffectiveUrl($response['effective_url']); } } elseif (empty($response['error'])) { $response['error'] = self::getNoRingResponseException($trans->request); } if (isset($response['error'])) { $trans->state = 'error'; $trans->exception = $response['error']; } } public static function fromRingRequest(array $request) { $options = []; if (isset($request['version'])) { $options['protocol_version'] = $request['version']; } if (!isset($request['http_method'])) { throw new \InvalidArgumentException('No http_method'); } return new Request( $request['http_method'], Core::url($request), isset($request['headers']) ? $request['headers'] : [], isset($request['body']) ? Stream::factory($request['body']) : null, $options ); } public static function getNoRingResponseException(RequestInterface $request) { $message = <<<EOT
Sending the request did not return a response, exception, or populate the
transaction with a response. This is most likely due to an incorrectly
implemented RingPHP handler. If you are simply trying to mock responses,
then it is recommended to use the GuzzleHttp\Ring\Client\MockHandler.
EOT;
return new RequestException($message, $request); } } 
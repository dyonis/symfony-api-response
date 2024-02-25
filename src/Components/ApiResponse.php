<?php

namespace App\Components;

use Symfony\Component\HttpFoundation\JsonResponse;

/*
 * This class is a wrapper for JsonResponse
 * It adds some useful methods for API responses
 */
class ApiResponse extends JsonResponse
{
    protected bool $addResult;
    protected bool $falseResultOnErrors = true;

    /**
     * @param bool $addResult Add to response bool `result` field
     * @param bool $falseResultOnErrors Change result to 'false' if at least one error was added
     */
    public function __construct(
        bool $addResult = true,
        bool $falseResultOnErrors = true,
    ){
        parent::__construct();

        $this->addResult = $addResult;
        $this->falseResultOnErrors = $falseResultOnErrors;

        $this->headers->set('Content-Type', 'application/json');

        // All response data as array
        $this->data = [];

        if ($this->addResult) {
            $this->setResult(true);
        }
    }

    /**
     * Add 'message' param to response
     */
    public function setMessage(?string $message): self
    {
        if ($message) {
            $this->data['msg'] = $message;
        }

        return $this;
    }

    /**
     * Set 'result' param value
     */
    public function setResult(bool $result): self
    {
        if (!$this->addResult) {
            return $this;
        }

        // don't change on errors
        if($this->falseResultOnErrors && $this->getErrors())
            return $this;

        $this->data['result'] = $result;
        $this->update();

        return $this;
    }

    public function setData(mixed $data = []): static
    {
        $this->data['data'] = $data;

        if ($data) {
            $this->update();
        }

        return $this;
    }

    public function getErrors(): array
    {
        return $this->data['errors'] ?? [];
    }

    public function addErrors(array $errors): self
    {
        if ($this->falseResultOnErrors) {
            $this->setResult(false);
        }

        $this->data['errors'] = array_merge($this->getErrors(), $errors);
        $this->update();

        return $this;
    }

    public function addError(string $error, string $category = null): self
    {
        if ($category) {
            $this->addErrors([$category => $error]);
        } else {
            $this->addErrors([$error]);
        }

        return $this;
    }

    public function apiRedirect(string $url, int $statusCode = 302): self
    {
        $this->data['redirect'] = [
                'url' => $url,
                'status' => $statusCode,
            ];
        $this->update();

        return $this;
    }

    public function addHtml(string $string): self
    {
        $this->data['html'] = $string;
        $this->update();

        return $this;
    }

    protected function update(): static
    {
        $this->content = json_encode($this->data);

        return $this;
    }
}

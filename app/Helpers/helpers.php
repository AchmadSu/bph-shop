<?php

use Illuminate\Support\Facades\Validator;

if (!function_exists('successResponse')) {
    function successResponse($message = "success", $data = [], $mergeData = false)
    {
        $response = [
            "success" => true,
            "status_code" => 200,
            "message" => $message
        ];

        if (!empty($data)) {
            if ($mergeData) {
                $response = array_merge($response, $data);
            } else {
                $response['data'] = $data;
            }
        }

        return $response;
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse($e = null, $array = [])
    {
        $statusCode = 500;
        $message = "Unknown error occurred";

        if ($e instanceof \Exception) {
            if ($e->getCode() > 0) {
                $statusCode = $e->getCode();
            }
            if (!empty($e->getMessage())) {
                $message = $e->getMessage();
            }
        }

        if (is_array($array)) {
            if (!empty($array['status_code'])) {
                $statusCode = $array['status_code'];
            }
            if (!empty($array['message'])) {
                $message = $array['message'];
            }
        }

        return [
            "success" => false,
            "status_code" => $statusCode,
            "message" => $message
        ];
    }
}

if (!function_exists('checkArrayRequired')) {
    function checkArrayRequired(array $data, array $required)
    {
        $missing = array_diff($required, array_keys($data));
        if (!empty($missing)) {
            $errorResponse = [
                "status_code" => 400,
                "success" => false,
                "message" => "Missing required fields: " . implode(', ', $missing)
            ];
            return response()->json($errorResponse, $errorResponse['status_code']);
        }
    }
}

if (!function_exists('validateFormData')) {
    function validateFormData(array $data, array $rules)
    {

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $status = 400;
            $success = false;
            $message = "";
            $messages = $validator->messages();
            $errors = $validator->errors()->toArray();

            foreach ($messages->all() as $errMessage) {
                $message = $message . $errMessage . " ";
            }

            $errorResponse = [
                "status_code" => $status,
                "success" => $success,
                "message" => $message,
                "errors" => $errors,
            ];
            return response()->json($errorResponse, $errorResponse['status_code']);
        }
    }
}

<?php

    namespace App\Traits;


    use App\Http\DooResponse;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Collection;
    use Symfony\Component\HttpFoundation\JsonResponse;

    trait ApiResponse
    {
        /**
         * @var \Illuminate\Support\Collection $_data
         */
        protected $_data;
        /**
         * @var string $_status
         */
        protected  $_status = ApiResponserConstantsInterface::STATUS_SUCCESS;

        /**
         * @param   array   $data
         *
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function sendResponse ($data = [])
        {
            $this->_setData($data);
            return $this->_makeResponse();
        }

        /**
         * @param   array   $data
         *
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function sendErrorResponse ($data = []): JsonResponse
        {
            $this->_setData($data);
            $this->_status = ApiResponserConstantsInterface::STATUS_ERROR;
            return $this->_makeResponse();
        }

        protected  function _makeResponse(): DooResponse
        {
            ob_clean();
            $headers = [
                "Access-Control-Allow-Origin"   => "http://localhost:80",
                "Access-Control-Expose-Headers" => "set-cookie",
                "Access-Control-Allow-Credentials" => "true",
                "Access-Control-Allow-Methods"     => "GET, POST, PUT, DELETE",
            ];
            return new DooResponse($this->convert_to_utf8_recursively($this->_formatResponse()), 200, $headers);
        }

        protected function convert_to_utf8_recursively ($dat)
        {
            if (is_string($dat)) {
                return mb_convert_encoding($dat, 'UTF-8', 'UTF-8');
            }
            elseif (is_array($dat)) {
                $ret = [];
                foreach ($dat as $i => $d) {
                    $ret[$i] = $this->convert_to_utf8_recursively($d);
                }
                return $ret;
            }
            else {
                return $dat;
            }
        }

        /**
         * @return array[]
         */
        protected  function _formatResponse (): array
        {
            $result = [
                'status' => $this->_status,
                'code'   => $this->_status === ApiResponserConstantsInterface::STATUS_SUCCESS
                    ? config('constants.apis.ERRCODE_NO_ERROR')
                    : config(
                        'constants.apis.ERRCODE_INTERNAL_ERROR'
                    ),
                'msg'    => '',
            ];

            $response_data  = [];
            $result['code'] = (int)($this->_getData()->get("code") ?? $result['code']);
            $result['msg']  = $this->_getData()->get("msg") ?? config("constants.apis.REST_ERRORS." . $result['code'], ["msg" => null])["msg"] ?? $result['msg'];

            if ($data = $this->_getData()->get("data", false)) {
                $response_data = $data;
            }

            $response = [
                'result' => $result,
            ];

            if (!empty($response_data)) {
                $response['response_data'] = $response_data;
            }


            return $response;
        }

        /**
         * @param $data
         */
        private  function _setData ($data): void
        {
            $this->_data = collect($data);
        }

        /**
         * @return \Illuminate\Support\Collection
         */
        private  function _getData (): Collection
        {
            return $this->_data ?? collect([]);
        }
    }

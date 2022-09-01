<?php
    namespace native\libs;

    use native\libs\Controller;

    /**
     * A Pipeline is a general / all-purpose controller with predefined behavior for CRUD operations
     * It is useful when dealing with public-facing data, accessible through the REST API
     * With as little as 3 parameters, it generated a fully working CRUD API resource with correct checks and responses
     * It does not prove very useful in specific contexts (authentication requirement, business behavior, etc), but it's
     * also a great blueprint for anyone who wants to make a controller for the first time
     */
    class Pipeline extends Controller {

        public $root;
        public $service;
        public $adapter;
        public $has_numeric_ids;

        public function __construct($params) {
            $this->root = $params['resource'];

            if ( isset($params['service']) )
                $this->services[$this->root] = $params['service'];
            // Default service when nothing's provided but the root
            else  {
                $service = new Service();
                $service->set_table($this->root);
                $service->set_schema([]);
                $this->services[$this->root] = $service;
            }

            if ( isset($params['adapter']) )
                $this->adapters[$this->root] = $params['adapter'];
            else {
                // Default adapter when nothing's provided but the root
                $adapter = new Adapter();
                $adapter->set_mapper('default' , [ 'pk' => 'id' ]);
                $this->adapters[$this->root] = $adapter;
            }

            if ( isset($params['has_numeric_ids']) )
                $this->has_numeric_ids = $params['has_numeric_ids'];
            elseif ( Options::get('ENCRYPTION_ENABLED') === true )
                $this->has_numeric_ids = false;
            else 
                $this->has_numeric_ids = true;
        }

        public function attach_to($router) {
            // GET /resources
            $router->get('/' . $this->root, [$this, 'list']);

            // GET /resources/:resource_id
            $router->get('/' . $this->root . '/:' .  $this->root . '_id', [$this, 'get']);

            // POST /resources
            $router->post('/' . $this->root, [$this, 'create']);

            // PUT /resources/:resource_id
            $router->put('/' . $this->root . '/:' . $this->root . '_id' , [$this, 'update']);

            // DELETE /resources/:resource_id
            $router->delete('/' . $this->root . '/:' . $this->root . '_id' , [$this, 'delete']);
        }

        public function get($req, $res) {
            $resource_id = $req->params[$this->root . '_id'];

            if($this->has_numeric_ids && !is_numeric($resource_id))
                return $res->send_not_found();

            if(!$this->services[$this->root]->exists($resource_id))
                return $res->send_not_found();

            $resource = $this->services[$this->root]->get($resource_id);
            $this->adapters[$this->root]->apply_one($resource);

            $resource_name = substr($this->root, 0, -1);

            return $res->send_success(
                [
                    'content' => [
                        "$resource_name" => $resource
                    ] 
                ]
            );
        }

        public function list($req, $res) {
            $per_page = (int) ($req->query['c'] ?? Options::get('PER_PAGE_DEFAULT'));
            $page = (int) ($req->query['p'] ?? Options::get('PAGE_DEFAULT'));

            if($per_page <= 0)
                return $res->send_not_found();

            if($page <= 0)
                return $res->send_not_found();

            // Retrieve data
            $resources = $this->services[$this->root]->find_many(
                [],
                [ 
                    'page' => $page, 
                    'per_page' => $per_page,
                ]
            );
            $count_resources = $this->services[$this->root]->find_count([]);
            $count_pages = ceil($count_resources / $per_page);

            $resource_name = $this->root;

            $res->send_success([
                'content' => [
                    "$resource_name" => $resources,
                    "count" => $count_resources,
                    'count_pages' => $count_pages,
                    'page' => $page,
                    'per_page' => $per_page
                ]
            ]);

        }

        public function create($req, $res) {
            $payload = $req->body;

            if ( !empty($this->services[$this->root]->get_schema()) )
                // Wrong payload
                if(!Validator::is_valid_schema($payload, $this->services[$this->root]->get_schema()))
                    return $res->send_malformed();
                // Proper payload
                else
                    Validator::enforce_schema($payload, $this->services[$this->root]->get_schema());
            // Service's Schema can be empty if provided by the pipeline by default
            else
                if (empty($payload))
                    return $res->send_malformed();

            $resource = $this->services[$this->root]->create($payload);
            $this->adapters[$this->root]->apply_one($resource);

            $resource_name = substr($this->root, 0, -1);

            return $res->send_success(
                [
                    'content' => [
                        "$resource_name" => $resource
                    ] 
                ]
            );
        }

        public function update($req, $res) {
            $resource_id = $req->params[$this->root . '_id'];

            if($this->has_numeric_ids && !is_numeric($resource_id))
                return $res->send_not_found();

            if(!$this->services[$this->root]->exists($resource_id))
                return $res->send_not_found();

            $payload = $req->body;

            if(!empty($this->services[$this->root]->get_schema())) { 
                if(!Validator::is_valid_schema($payload, $this->services[$this->root]->get_schema()))
                    return $res->send_malformed();

                Validator::enforce_schema($payload, $this->services[$this->root]->get_schema());
            }

            $resource = $this->services[$this->root]->update($resource_id, $payload);
            $this->adapters[$this->root]->apply_one($resource);

            $resource_name = substr($this->root, 0, -1);

            return $res->send_success(
                [
                    'content' => [
                        "$resource_name" => $resource
                    ] 
                ]
            );
        }

        public function delete($req, $res) {
            $resource_id = $req->params[$this->root . '_id'];

            if($this->has_numeric_ids && !is_numeric($resource_id))
                return $res->send_not_found();

            if(!$this->services[$this->root]->exists($resource_id))
                return $res->send_not_found();

            $this->services[$this->root]->delete($resource_id);

            return $res->send_success();
        }
    }

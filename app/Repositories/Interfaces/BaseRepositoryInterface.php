<?php

namespace App\Repositories\Interfaces;


/**
 * Interface AddressRepository.
 *
 * @package namespace App\Repositories\Interfaces;
 */
interface BaseRepositoryInterface
{
    //public function all();
    public function model();
    
	// CRUD

    public function create(array $data);



    public function update(array $data);

    public function delete($id);

    public function list(array $data);



}

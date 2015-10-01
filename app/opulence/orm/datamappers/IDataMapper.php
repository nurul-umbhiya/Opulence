<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for data mappers to implement
 */
namespace Opulence\ORM\DataMappers;

use Opulence\ORM\IEntity;
use Opulence\ORM\ORMException;

interface IDataMapper
{
    /**
     * Adds an entity to the database
     *
     * @param IEntity $entity The entity to add
     * @throws ORMException Thrown if the entity couldn't be added
     */
    public function add(IEntity &$entity);

    /**
     * Deletes an entity
     *
     * @param IEntity $entity The entity to delete
     * @throws ORMException Thrown if the entity couldn't be deleted
     */
    public function delete(IEntity &$entity);

    /**
     * Gets all the entities
     *
     * @return array The list of all the entities
     */
    public function getAll();

    /**
     * Gets the entity with the input Id
     *
     * @param int|string $id The Id of the entity we're searching for
     * @return IEntity The entity with the input Id
     * @throws ORMException Thrown if there was no entity with the input Id
     */
    public function getById($id);

    /**
     * Saves any changes made to an entity
     *
     * @param IEntity $entity The entity to save
     * @throws ORMException Thrown if the entity couldn't be saved
     */
    public function update(IEntity &$entity);
} 
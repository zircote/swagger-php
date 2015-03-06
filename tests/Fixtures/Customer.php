<?php
/**
 * @SWG\Definition()
 */
class Customer {
    
    /**
     * The firstname of the customer.
     * @var string
     * @SWG\Property()
     */
    public $firstname;
    
    /**
     * @var string The lastname of the customer.
     * @SWG\Property()
     */
    public $lastname;
    
    /**
     * @SWG\Property()
     * @var string[]
     */
    public $tags;
    
}

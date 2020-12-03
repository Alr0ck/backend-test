<?php
namespace App\Transformers;

use App\Models\Company;
use League\Fractal\TransformerAbstract;

class CompanyTransformer extends TransformerAbstract
{
    protected $availableIncludes = [];

	public function transform(Company $c)
	{
	    return [
            'id'            => (int) $c->id,
            'name'          => $c->name,
            'email'         => $c->email,
            'logo'          => $c->logo,
            'website'       => $c->website,
            'dateCreated'   => $c->dateCreated,
            'dateUpdated'   => $c->dateUpdated,
            'dateDeleted'   => $c->dateDeleted
	    ];
    }

}

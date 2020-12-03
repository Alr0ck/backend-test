<?php

namespace App\Http\Controllers\V1\Company;

use App\Http\Controllers\V1\Controller;
use Illuminate\Http\Request;
use App\Repositories\Company\ICompanyRepository;
use App\Models\Company;
use App\Transformers\CompanyTransformer;
use Illuminate\Auth\Access\AuthorizationException;

class CompanyController extends Controller
{
    protected $companyRepo;

    public function __construct(
        ICompanyRepository $companyRepo
    )
    {
        $this->companyRepo = $companyRepo;
    }
    
    public function index(Request $request)
    {
        $paginator =  $this->companyRepo->getCompanies($request);
        return $this->paginateResponse(
            $request,
            $paginator, Company::$Type,
            new CompanyTransformer()
        );
    }

    public function store(Request $request)
    {
        $attr = $this->resolveRequest($request);
        $company = $this->companyRepo->createCompany($attr);
        
        return $this->crateUpdateResponse(
            $request,
            $company,
            Company::$Type,
            new CompanyTransformer()
        );
    }

    public function update(Request $request, $id)
    {
        $attr = $this->resolveRequest($request);
        $company = $this->companyRepo->updateCompany($attr,$id);

        if($company == null){
            throw new AuthorizationException("You Not Authorized Access End Point");
        }
        
        return $this->crateUpdateResponse(
            $request,
            $company,
            Company::$Type,
            new CompanyTransformer()
        );
    }

    public function show(Request $request, $id)
    {
        $company = $this->companyRepo->getCompanyById($id);
        
        if($company != null){
            return $this->singleResponse(
                $request,
                $company,
                Company::$Type,
                new CompanyTransformer()
            );
        }

        return $this->emptyResponse("Data Tidak Ada");
    }

    public function destroy(Request $request, $id)
    {
        $company = $this->companyRepo->deleteCompany($id);
        if(!$company){
            throw new AuthorizationException("You Not Authorized Access End Point");
        }
        return $this->deleteResponse();
    }
}

<?php

namespace App\Http\Controllers\User;

use DB;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Resources\TemplateResource;
use App\Models\Template;
use App\Services\TemplateService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Validator;

class TemplateController extends BaseController
{
        public function crousel(Request $request)
    {
        
        return $this->templateService()->createCrousel($request);
    }
    public function sendCrouselMessage(Request $request)
    {
        return $this->templateService()->sendCrousel($request);
    }
    private function templateService()
    {
        return new TemplateService(session()->get('current_organization'));
    }
    public function upload(Request $request)
    {
        return $this->templateService()->upload($request);
    }
    public function index(Request $request, $uuid = null)
    {
        return $this->templateService()->getTemplates($request, $uuid, $request->query('search'));
    }

    public function create(Request $request)
    {
        return $this->templateService()->createTemplate($request);
    }

    public function update(Request $request, $uuid)
    {
        return $this->templateService()->updateTemplate($request, $uuid);
    }

    public function delete($uuid)
    {
        return $this->templateService()->deleteTemplate($uuid);
    }

    public function uploadfile(Request $request)
    {
        return $this->templateService()->uploadfile($request);
    }
}
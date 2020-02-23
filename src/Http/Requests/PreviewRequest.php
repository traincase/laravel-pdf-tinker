<?php

namespace Traincase\LaravelPdfTinker\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Traincase\HtmlToPdfTinker\PdfTinkerManager;

class PreviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function rules()
    {
        /** @var PdfTinkerManager $manager */
        $manager = $this->container->make(PdfTinkerManager::class);

        return [
            'html' => 'required|string',
            'driver' => [
                'required',
                'string',
                'in:' . join(',', $manager->getRegisteredDrivers()),
            ],
            'mode' => 'required|string',
            'options' => 'required|string',
        ];
    }
}

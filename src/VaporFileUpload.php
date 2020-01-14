<?php

namespace VisualHost\VaporFileUpload;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class VaporFileUpload extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'vapor-file-upload';

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $files = explode(',', $request->get($requestAttribute));

            foreach ($files as &$file) {
                if (!Str::startsWith($file, 'tmp')) {
                    continue;
                }

                $folder = str_replace('_', '-', $requestAttribute);
                $newFile = str_replace('tmp/', '', "uploads/$folder/$file");
                Storage::disk('s3')->copy(explode('.', $file)[0], $newFile);
                $file = $newFile;
            }

            $model->{$attribute} = $files;
        }
    }

    public function allowMultiple(string $allowMultiple): self
    {
        return $this->withMeta(['allowMultiple' => $allowMultiple]);
    }

    public function maxFiles(string $maxFiles): self
    {
        return $this->withMeta(['maxFiles' => $maxFiles]);
    }
}

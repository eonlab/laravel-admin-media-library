<?php

namespace Eonlab\LaravelAdmin\MediaLibrary;

use Encore\Admin\Form\Field\MultipleFile;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaLibraryMultipleFile extends MultipleFile
{
    use MediaLibraryBase;

    protected $view = 'admin::form.multiplefile';

    public function fill($data)
    {
        parent::fill($data);

        $value = $this->form->model()->getMedia($this->column());

        foreach ($value as $key => $media) {
            $this->value[$key] = $media->id;
        }
    }

    protected function sortFiles($order)
    {
        $new = explode(',',$order);
        $n = 1;
        foreach($new AS $id){
            $media = Media::where('id', $id)->first();
            $media->order_column = $n;
            $media->save();
            $n++;
        }

        return $new;
    }
    
    public function setOriginal($data)
    {
        $value = $this->form->model()->getMedia($this->column());

        foreach ($value as $key => $media) {
            $this->original[$key] = $media->id;
        }
    }

    protected function prepareForeach(UploadedFile $file = null)
    {
        $this->name = $this->getStoreName($file);

        return $this->uploadMedia($file);
    }

    protected function initialPreviewConfig()
    {

        $medias = Media::whereIn('id', $this->value ?: [])->orderBy('order_column', 'ASC')->groupBy('id')->get();

        $config = [];
        foreach ($medias as $media) {
            $config[] = $this->getPreviewEntry($media);
        }

        return $config;
    }

    public function destroy($key)
    {
        $files = $this->original ?: [];

        foreach ($files as $fileKey => $file) {
            if ($file == $key) {
                $media = Media::whereId($key)->first();
                $media->delete();
            }
        }

        return array_values($files);
    }
}

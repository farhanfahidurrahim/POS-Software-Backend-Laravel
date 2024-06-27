<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\VariationTemplateRequest;
use App\Http\Resources\VariationTemplateResource;
use App\Models\VariationTemplate;
use App\Models\VariationValueTemplate;
use Illuminate\Http\Request;

class VariationTemplateController extends Controller
{
    public function index()
    {
        $variationtemplates = VariationTemplate::with('values')->get();
        if ($variationtemplates->isEmpty()) {
            return response()->json(['message' => 'No Variation Template found'], 200);
        }
        return VariationTemplateResource::collection($variationtemplates);
    }

    public function store(VariationTemplateRequest $request)
    {
        try {
            $variationTemplate = VariationTemplate::create([
                'name' => $request->input('name'),
            ]);

            $variationValueTemplatesData = $request->input('value');
            $variationValueTemplates = [];

            foreach ($variationValueTemplatesData as $valueTemplateData) {
                $fullValueName = $request->input('name') . '-' . $valueTemplateData;

                VariationValueTemplate::create([
                    'name' => $fullValueName,
                    'variation_template_id' => $variationTemplate->id,
                ]);
            }
            return response()->json([
                'message' => 'Variation Template created successfully',
                'data'    => new VariationTemplateResource($variationTemplate)
            ], 200);
        } catch (\Exception $e) {
            // Handle the exception here
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $variationtemplate = VariationTemplate::with('values')->find($id);
        if (!$variationtemplate) {
            return response()->json(['message' => 'Variation Template not found'], 404);
        }
        return new VariationTemplateResource($variationtemplate);
    }

    public function update(VariationTemplateRequest $request, $id)
    {
        try {
            $variationTemplate = VariationTemplate::find($id);
            if (!$variationTemplate) {
                return response()->json(['message' => 'VariationTemplate not found'], 404);
            }
            $variationTemplate->update([
                'name'      => $request->input('name'),
            ]);
            foreach ($request->value as $key => $valueTemplate) {
                if ($request->value_id[$key] == null) {
                    $variationValueTemplate = new VariationValueTemplate();
                    $variationValueTemplate->variation_template_id = $id;
                    $variationValueTemplate->name = $valueTemplate;
                    $variationValueTemplate->save();
                } else {
                    $values = VariationValueTemplate::find($request->value_id[$key]);
                    $values->name = $valueTemplate;
                    $values->update();
                }
            }
            return response()->json([
                'message' => 'Variation Template updated successfully',
                'data'    => new VariationTemplateResource($variationTemplate),
            ], 200);
        } catch (\Exception $e) {
            // Handle the exception here
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $variationtemplate = VariationTemplate::find($id);

        if ($variationtemplate) {
            $variationtemplate->delete();
            return response()->json(['message' => 'Variation Template deleted successfully'], 200);
        }
        return response()->json(['message' => 'Variation Template not found'], 404);
    }
}
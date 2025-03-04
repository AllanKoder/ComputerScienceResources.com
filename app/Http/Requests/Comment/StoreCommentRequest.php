<?php

namespace App\Http\Requests\Comment;

use App\Services\ModelResolverService;
use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Closure;

class StoreCommentRequest extends FormRequest
{
    protected $modelResolver;

    public function __construct(ModelResolverService $modelResolver)
    {
        parent::__construct();
        $this->modelResolver = $modelResolver;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "commentable_id" => ['required', 'integer'],
            "commentable_type" => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail) {
                    $id = request('commentable_id');
                    $model = $this->modelResolver->resolve($value, $id);
                    
                    if ($model == null)
                    {
                        $fail("commentable id and type does not exist.");
                    }
                },
            ],
            "content" => ["required", "string", "max:4000"],
            "parent_comment_id" => ["nullable", "exists:App\Models\Comment,id"]
        ];
    }
}

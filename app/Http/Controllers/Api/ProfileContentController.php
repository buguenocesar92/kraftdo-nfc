<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProfileContentRequest;
use App\Http\Requests\UpdateProfileContentRequest;
use App\Http\Requests\CreateSocialLinkRequest;
use App\Http\Resources\ProfileContentResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\ProfileContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ProfileContentController extends Controller
{
    use ApiResponseTrait;

    protected ProfileContentService $profileContentService;

    public function __construct(ProfileContentService $profileContentService)
    {
        $this->profileContentService = $profileContentService;
    }

    /**
     * Create profile content for a dynamic content
     */
    public function createProfileContent(CreateProfileContentRequest $request, int $dynamicContentId): JsonResponse
    {
        try {
            $profileContent = $this->profileContentService->createProfileContent(
                $dynamicContentId,
                $request->validated()
            );

            return $this->successResponse(
                new ProfileContentResource($profileContent),
                'Contenido de perfil creado exitosamente',
                201
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido dinámico no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor: ' . $e->getMessage());
        }
    }

    /**
     * Get profile content by dynamic content ID
     */
    public function getProfileContent(int $dynamicContentId): JsonResponse
    {
        try {
            $profileContent = $this->profileContentService->getProfileContent($dynamicContentId);

            return $this->successResponse(
                new ProfileContentResource($profileContent),
                'Contenido de perfil obtenido exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido de perfil no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Update profile content
     */
    public function updateProfileContent(UpdateProfileContentRequest $request, int $profileId): JsonResponse
    {
        try {
            $profileContent = $this->profileContentService->updateProfileContent(
                $profileId,
                $request->getUpdateData()
            );

            return $this->successResponse(
                new ProfileContentResource($profileContent),
                'Contenido de perfil actualizado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Contenido de perfil no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Get social links for a profile
     */
    public function getSocialLinks(int $profileId): JsonResponse
    {
        try {
            $socialLinks = $this->profileContentService->getSocialLinks($profileId);

            return $this->successResponse(
                $socialLinks,
                'Enlaces sociales obtenidos exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Perfil no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Create social link for a profile
     */
    public function createSocialLink(CreateSocialLinkRequest $request, int $profileId): JsonResponse
    {
        try {
            $socialLink = $this->profileContentService->createSocialLink(
                $profileId,
                $request->validated()
            );

            return $this->successResponse(
                $socialLink,
                'Enlace social creado exitosamente',
                201
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Perfil no encontrado');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse('Datos de validación incorrectos', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }

    /**
     * Delete social link
     */
    public function deleteSocialLink(int $linkId): JsonResponse
    {
        try {
            $this->profileContentService->deleteSocialLink($linkId);

            return $this->successResponse(
                null,
                'Enlace social eliminado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Enlace social no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse('Error interno del servidor');
        }
    }
}
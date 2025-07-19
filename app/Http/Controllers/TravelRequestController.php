<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTravelRequestRequest;
use App\Http\Requests\UpdateTravelRequestRequest;
use App\Services\TravelRequestService;
use App\DTOs\TravelRequestDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TravelRequestController extends Controller
{
    public function __construct(
        private TravelRequestService $travelRequestService
    ) {}


    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'status',
                'destination', 
                'departure_date_from',
                'departure_date_to',
                'return_date_from',
                'return_date_to',
                'user_id',
                'created_from',
                'created_to',
                'order_by',
                'order_direction'
            ]);

            $perPage = $request->get('per_page', 15);
            $travelRequests = $this->travelRequestService->getAll($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $travelRequests->items(),
                'meta' => [
                    'current_page' => $travelRequests->currentPage(),
                    'last_page' => $travelRequests->lastPage(),
                    'per_page' => $travelRequests->perPage(),
                    'total' => $travelRequests->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar pedidos de viagem',
                'error' => $e->getMessage()
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function store(StoreTravelRequestRequest $request): JsonResponse
    {
        try {
            $request['user_id'] = Auth::id();
            $dto = TravelRequestDTO::fromArray($request->array());
            $travelRequest = $this->travelRequestService->create($dto);

            return response()->json([
                'success' => true,
                'message' => 'Pedido de viagem criado com sucesso',
                'data' => $travelRequest->load('user')
            ], 201);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'error' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar pedido de viagem',
                'error' => $e->getMessage()
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $travelRequest = $this->travelRequestService->findById($id);

            if (!$travelRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido de viagem não encontrado'
                ], ResponseAlias::HTTP_NOT_FOUND);
            }

            $user = Auth::user();
            if ($user->role !== 'admin' && $travelRequest->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $travelRequest
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar pedido de viagem',
                'error' => $e->getMessage()
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateStatus(string $id, UpdateTravelRequestRequest $request): JsonResponse
    {
        try {
            $travelRequest = $this->travelRequestService->updateStatus(
                $id,
                $request->status,
                Auth::user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Status do pedido atualizado com sucesso',
                'data' => $travelRequest->load('user')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status do pedido',
                'error' => $e->getMessage()
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    public function cancelApproved(string $id): JsonResponse
    {
        try {
            $travelRequest = $this->travelRequestService->cancelApprovedRequest(
                $id,
                Auth::user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Pedido aprovado cancelado com sucesso',
                'data' => $travelRequest->load('user')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar pedido aprovado',
                'error' => $e->getMessage()
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    public function myRequests(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status']);
            $filters['user_id'] = Auth::id();

            $perPage = $request->get('per_page', 15);
            $travelRequests = $this->travelRequestService->getAll($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $travelRequests->items(),
                'meta' => [
                    'current_page' => $travelRequests->currentPage(),
                    'last_page' => $travelRequests->lastPage(),
                    'per_page' => $travelRequests->perPage(),
                    'total' => $travelRequests->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar seus pedidos de viagem',
                'error' => $e->getMessage()
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
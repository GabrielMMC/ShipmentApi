<?php

namespace App\Http\Controllers;

use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingController extends Controller
{
    public function create(Request $request)
    {
        DB::beginTransaction();
        // Existe mais de um centro de onde sairá a entrega?
        // O endereço de remetente é o da farmácia ou centro de distribuições da região?
        // Como controlar os volumes?
        // O valor do frete deve ser passado para a conta na Cielo e descontado da carteira do Melhor Envio?
        // A API não possui webhook, mas dá para fazer o controle no rastreio da entrega

        try {
            list($from, $to, $service, $products) = [$request->from, $request->to, $request->service, $request->products];

            $freight = Shipping::calculate($from, $to, $service, $products);
            $checkout = Shipping::checkout([$freight->id]);
            $label = Shipping::generateLabel([$freight->id]);
            $label = Shipping::printLabel([$freight->id]);

            DB::commit();
            return response()->json([
                'message' => 'Pedido gerado com sucesso!',
                'label' => $label,
                'id' => $freight->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Houve um erro ao processar o pedido, verifique seus dados',
                'error' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
            ]);
        }
    }

    public function get($id)
    {
        try {
            $shipping = Shipping::tracking($id);

            return response()->json([
                'shipping' => $shipping,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Houve um erro ao carregar os dados do pedido',
                'error' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
            ]);
        }
    }
}

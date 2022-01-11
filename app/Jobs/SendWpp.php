<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SendWpp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $listaTotal;
    private $item;
    private $url;
    private $urlImage;
    private $header;
    private $img;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($listaTotal, $item, $url, $urlImage, $header , $img)
    {
        $this->listaTotal = $listaTotal;
        $this->item = $item;
        $this->url = $url;
        $this->urlImage = $urlImage;
        $this->header = $header;
        $this->img = $img;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){

        for($i = 0; $i < count($this->listaTotal); $i++){

            if($this->listaTotal[$i]['bairro'] == $this->item['bairro']){

                $contador = 0;

                $dataImage = array(
                    "phone" => substr($this->listaTotal[$i]['telefone'], 0 , 4) . substr($this->listaTotal[$i]['telefone'], 5),
                    "base64" => "data:image/png;base64,".$this->img,
                    "isGroup" => false
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->urlImage);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataImage));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $return = curl_exec($ch);
                curl_close($ch);

                sleep(1);

                $nome = $this->listaTotal[$i]['nome'];

                $data = array(
                    "phone" =>  substr($this->listaTotal[$i]['telefone'], 0 , 4) . substr($this->listaTotal[$i]['telefone'], 5),
                    "message" =>  "*$nome* \n\n".$this->item['mensagem'],
                    "isGroup" => false
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $return = curl_exec($ch);
                curl_close($ch);
                
                sleep(40);

                info("Enviado para " . $this->listaTotal[$i]['nome'] . " do bairro " . $this->listaTotal[$i]['bairro']);

                DB::table('contador')->where('id', 1)->update(['enviadas' => DB::raw('enviadas + 1') , 'updated_at' => date('Y-m-d H:i:s')]);

            }

        }

        DB::table('contador')->where('id', 1)->update(['enviadas' => DB::raw('0') , 'updated_at' => date('Y-m-d H:i:s')]);

        info("Foi");

    }
}

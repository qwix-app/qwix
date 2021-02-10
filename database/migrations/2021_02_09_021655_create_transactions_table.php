<?php

use App\Models\Normal;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedDouble('value');
            $table->foreignIdFor(Normal::class, 'payer_id');
            $table->unsignedDouble('payer_prev_bal_snapshot');
            $table->unsignedDouble('payer_cur_bal_snapshot');
            $table->foreignIdFor(User::class, 'payee_id');
            $table->unsignedDouble('payee_prev_bal_snapshot');
            $table->unsignedDouble('payee_cur_bal_snapshot');
            $table->boolean('successful')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}

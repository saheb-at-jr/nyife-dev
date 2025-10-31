use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('razorpay_payments')) {
            Schema::create('razorpay_payments', function (Blueprint $table) {
                $table->id();
                $table->string('payment_id')->unique(); // Razorpay payment ID
                $table->string('order_id')->nullable(); // if you use orders
                $table->string('status')->default('created'); // created, authorized, captured, failed, refunded
                $table->unsignedBigInteger('organization_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('plan_id')->nullable();
                $table->decimal('amount', 12, 2)->nullable();
                $table->json('raw_payload')->nullable(); // Store full webhook payload
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('razorpay_payments');
    }
};

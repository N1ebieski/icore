<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use N1ebieski\ICore\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\ValueObjects\Role\Name;
use N1ebieski\ICore\ValueObjects\User\Status;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Contracts\Validation\Factory as Validator;

class RegisterSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icore:superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register super admin first time.';

    /**
     * Undocumented function
     *
     * @param User $user
     */
    public function __construct(
        protected User $user,
        protected Lang $lang,
        protected Validator $validator,
        protected Hasher $hasher,
        protected Carbon $carbon
    ) {
        parent::__construct();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function authorize(): bool
    {
        return $this->user->whereHas('roles', function (Builder $query) {
            $query->where('name', Name::SUPER_ADMIN);
        })->count() === 0;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return boolean
     */
    protected function validate(array $attributes): bool
    {
        $validator = $this->validator->make(
            [
                'name' => $attributes['name'],
                'password' => $attributes['password'],
                'password_confirmation' => $attributes['password_confirmation'],
                'email' => $attributes['email']
            ],
            [
                'name' => ['required', 'alpha_dash', 'max:255'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
            ]
        );

        foreach ($validator->errors()->getMessages() as $key => $value) {
            $this->error($key . ": " . $value[0] . "\n");
        }

        return !$validator->fails();
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    protected function prepareAttributes(): array
    {
        while (true) {
            $attributes = [
                'name' => $this->ask($this->lang->get('icore::auth.name.label')),
                'password' => $this->ask($this->lang->get('icore::auth.password')),
                'password_confirmation' => $this->ask($this->lang->get('icore::auth.password_confirm')),
                'email' => $this->ask($this->lang->get('icore::auth.address.label'))
            ];

            if ($this->validate($attributes)) {
                break;
            }
        }

        return $attributes;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return User
     */
    protected function create(array $attributes): User
    {
        $user = $this->user->create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $this->hasher->make($attributes['password']),
            'email_verified_at' => $this->carbon->now(),
            'status' => Status::ACTIVE
        ]);

        $user->assignRole([Name::SUPER_ADMIN, Name::ADMIN, Name::USER]);

        $this->info($this->lang->get('icore::superadmin.success.store'));

        return $user;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->authorize()) {
            $this->error($this->lang->get('icore::superadmin.error.exist'));

            exit;
        }

        $this->create($this->prepareAttributes());
    }
}

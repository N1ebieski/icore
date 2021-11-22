<?php

namespace N1ebieski\ICore\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use N1ebieski\ICore\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Contracts\Validation\Factory as Validator;

class RegisterSuperAdminCommand extends Command
{
    /**
     * Undocumented variable
     *
     * @var User
     */
    protected $user;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented variable
     *
     * @var Validator
     */
    protected $validator;

    /**
     * Undocumented variable
     *
     * @var Hasher
     */
    protected $hasher;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

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
        User $user,
        Lang $lang,
        Validator $validator,
        Hasher $hasher,
        Carbon $carbon
    ) {
        parent::__construct();

        $this->user = $user;

        $this->lang = $lang;
        $this->validator = $validator;
        $this->hasher = $hasher;
        $this->carbon = $carbon;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function authorize(): bool
    {
        return $this->user->whereHas('roles', function ($query) {
            $query->where('name', 'super-admin');
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
            'status' => User::ACTIVE
        ]);

        $user->assignRole(['super-admin', 'admin', 'user']);

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

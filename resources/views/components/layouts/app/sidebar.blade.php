            <aside :class="{ 'w-full md:w-64': sidebarOpen, 'w-0 md:w-16 hidden md:block': !sidebarOpen }"
                class="bg-sidebar text-sidebar-foreground border-r border-gray-200 dark:border-gray-700 sidebar-transition overflow-hidden">
                <!-- Sidebar Content -->
                <div class="h-full flex flex-col">
                    <!-- Sidebar Menu -->
                    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4">
                        <ul class="space-y-1 px-2">
                            <!-- Dashboard -->
                            @hasrole('super-admin|admin|sales-manager|operation-manager')
                            <x-layouts.sidebar-link href="{{ route('dashboard') }}" icon='fas-house'
                                :active="request()->routeIs('dashboard*')">Dashboard</x-layouts.sidebar-link>
                            @endhasrole
                            {{-- <x-layouts.sidebar-link href="{{ route('pricing-estimates.index') }}" icon='fas-tags'
                                :active="request()->routeIs('pricing-estimates*')">Pricing Estimate</x-layouts.sidebar-link> --}}
                                
                            <x-layouts.sidebar-two-level-link-parent title="Pricing Estimate" icon="fas-tags"
                                :active="request()->routeIs('pricing-estimates*',)">
                                @hasrole('super-admin|admin|sales-manager|operation-manager')
                                <x-layouts.sidebar-two-level-link href="{{ route('pricing-estimates.index')  }}" icon='fas-clock'
                                    :active="request()->routeIs('pricing-estimates.index')">All P.E</x-layouts.sidebar-two-level-link>
                                @endhasrole
                                @hasrole('super-admin|admin|sales-manager|sales-representative|operation-manager')
                                <x-layouts.sidebar-two-level-link href="{{ route('pricing-estimates.my')  }}" icon='fas-list'
                                    :active="request()->routeIs('pricing-estimates.edit','pricing_estimate.create','pricing-estimates.my', 'pricing-estimates.show')">My P.E</x-layouts.sidebar-two-level-link>
                                @endhasrole
                            </x-layouts.sidebar-two-level-link-parent>

                            @hasrole('super-admin|admin|sales-manager|operation-manager|sales-representative')
                            <x-layouts.sidebar-link href="{{ route('quotations.index') }}" icon='fas-file-invoice'
                                :active="request()->routeIs('quotations*')">Quotations</x-layouts.sidebar-link>

                            <x-layouts.sidebar-link href="{{ route('temporary_quotations.index') }}" icon='fas-file-invoice'
                                :active="request()->routeIs('temporary_quotations*')">Temporary Quotation</x-layouts.sidebar-link>

                            <x-layouts.sidebar-link href="{{ route('sales-orders.index') }}" icon='fas-shopping-cart'
                                :active="request()->routeIs('sales-orders*')">Sales Order</x-layouts.sidebar-link>
                            @endhasrole
                            @hasrole('super-admin|admin|operation-manager|operation-staff')
                            <x-layouts.sidebar-link href="{{ route('job-orders.index') }}" icon='fas-briefcase'
                                :active="request()->routeIs('job-orders*')">Job Order</x-layouts.sidebar-link>    

                            <x-layouts.sidebar-link href="{{ route('production-detail-reports.index') }}" icon='fas-file-alt'
                                :active="request()->routeIs('production-detail-reports*')">Product Detail Report</x-layouts.sidebar-link>
                            @endhasrole
                            
                            @php
                                $latestConversationId = \App\Models\Message::whereHas('conversation.users', function ($q) {
                                        $q->where('user_id', auth()->id());
                                    })
                                    ->latest('created_at')
                                    ->value('conversation_id');
                            @endphp
                            <x-layouts.sidebar-link href="{{ route('messages.index', $latestConversationId) }}" icon='fas-envelope'
                                :active="request()->routeIs('messages*')">Messages</x-layouts.sidebar-link>


                            @hasrole('super-admin|admin')
                            <x-layouts.sidebar-two-level-link-parent title="Account Managements" icon="fas-user-cog"
                                :active="request()->routeIs('users*', 'roles*','permissions*')">
                                <x-layouts.sidebar-two-level-link href="{{ route('users.index')  }}" icon='fas-user'
                                    :active="request()->routeIs('users*')">Users</x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="{{ route('roles.index')  }}" icon='fas-user-shield'
                                    :active="request()->routeIs('roles*')">Roles</x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="{{ route('permissions.index')  }}" icon='fas-user-lock'
                                    :active="request()->routeIs('permissions*')">Permission</x-layouts.sidebar-two-level-link>
                            </x-layouts.sidebar-two-level-link-parent>
                            <x-layouts.sidebar-two-level-link-parent title="Human Resource" icon="fas-id-card"
                                :active="request()->routeIs('employees*', 'departments*', 'positions*')">
                                <x-layouts.sidebar-two-level-link href="{{ route('employees.index')  }}" icon='fas-user-tie'
                                    :active="request()->routeIs('employees*')">Employees</x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="{{ route('departments.index')  }}" icon='fas-building'
                                    :active="request()->routeIs('departments*')">Departments</x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="{{ route('positions.index')  }}" icon='fas-briefcase'
                                    :active="request()->routeIs('positions*')">Positions</x-layouts.sidebar-two-level-link>
                            </x-layouts.sidebar-two-level-link-parent>
                            @endhasrole
                            
                     
                            <x-layouts.sidebar-two-level-link-parent title="Master Data" icon="fas-database"
                                :active="request()->routeIs('items*', 'customers*', 'currencies*', 'moulds*','materials*','units*')">
                                <x-layouts.sidebar-two-level-link href="{{ route('customers.index')  }}" icon='fas-user'
                                    :active="request()->routeIs('customers*')">Customers</x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="{{ route('items.index')  }}" icon='fas-box'
                                    :active="request()->routeIs('items*')">Items</x-layouts.sidebar-two-level-link>
                                @hasrole('super-admin|admin')
                                <x-layouts.sidebar-two-level-link href="{{ route('currencies.index')  }}" icon='fas-peso-sign'
                                    :active="request()->routeIs('currencies*')">Currency</x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="{{ route('moulds.index')  }}" icon='fas-industry'
                                    :active="request()->routeIs('moulds*')">Moulds</x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="{{ route('materials.index')  }}" icon='fas-cubes'
                                    :active="request()->routeIs('materials*')">Material</x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="{{ route('units.index')  }}" icon='fas-balance-scale'
                                    :active="request()->routeIs('units*')">Unit</x-layouts.sidebar-two-level-link>
                                @endhasrole
                            </x-layouts.sidebar-two-level-link-parent>
                            
                            <x-layouts.sidebar-link href="{{ route('settings.profile.edit') }}" icon='fas-cog'
                                :active="request()->routeIs('settings*')">Settings</x-layouts.sidebar-link>
               

                            {{-- <!-- Example two level -->
                            <x-layouts.sidebar-two-level-link-parent title="Example two level" icon="fas-house"
                                :active="request()->routeIs('two-level*')">
                                <x-layouts.sidebar-two-level-link href="#" icon='fas-house'
                                    :active="request()->routeIs('two-level*')">Child</x-layouts.sidebar-two-level-link>
                            </x-layouts.sidebar-two-level-link-parent>

                            <!-- Example three level -->
                            <x-layouts.sidebar-two-level-link-parent title="Example three level" icon="fas-house"
                                :active="request()->routeIs('three-level*')">
                                <x-layouts.sidebar-two-level-link href="#" icon='fas-house'
                                    :active="request()->routeIs('three-level*')">Single Link</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-three-level-parent title="Third Level" icon="fas-house"
                                    :active="request()->routeIs('three-level*')">
                                    <x-layouts.sidebar-three-level-link href="#" :active="request()->routeIs('three-level*')">
                                        Third Level Link
                                    </x-layouts.sidebar-three-level-link>
                                </x-layouts.sidebar-three-level-parent>
                            </x-layouts.sidebar-two-level-link-parent> --}}
                        </ul>
                    </nav>
                </div>
            </aside>

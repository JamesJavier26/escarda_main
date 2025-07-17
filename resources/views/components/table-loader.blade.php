<div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow animate-pulse">
    <div class="flex justify-between items-center mb-4">
        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/6"></div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    @foreach([1,2,3,4] as $col)
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mx-auto"></div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach(range(1, 5) as $row)
                <tr>
                    @foreach(range(1, 4) as $col)
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="flex justify-between items-center mt-4">
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
    </div>
</div>
<?php

use App\Enums\DiscountTypes;
use App\Models\Campaign\CampaignProduct;
use App\Models\PrimeView\PrimeViewProduct;
use App\Models\Sell\SellWithUsPage;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

if (!function_exists('rowIndex')) {
    function rowIndex($iteration, $currentPage, $perPage)
    {
        return $iteration + ($currentPage - 1) * $perPage;
    }
}

if (!function_exists('skipTypeConvert')) {
    function skipTypeConvert(&$data, $skipKeys = [])
    {
        if (!is_array($data)) {
            return;
        }
        array_walk_recursive($data, function (&$value, $key) use ($skipKeys) {
            if (is_numeric($value) && !in_array($key, $skipKeys, true)) {
                $value = !str_contains($value, '.') ? (int) $value : (float) $value;
            }
        });
    }
}

if (!function_exists('uniqueForMerchant')) {
    function uniqueForMerchant(string $table, string $column = 'name')
    {
        return Rule::unique($table)->where(function ($query) {
            $query->where('merchant_id', auth()->user()->merchant?->id);
        });
    }
}

if (!function_exists('dateFormat')) {
    function dateFormat($date)
    {
        return date('M d, Y; h:i A', strtotime($date));
    }
}

if (!function_exists('assetUrl')) {
    function assetUrl($file_path)
    {
        return Storage::disk('public')->url($file_path);
    }
}

if (!function_exists('discountCalculation')) {
    function discountCalculation($price, $discount): float
    {
        return ($price * $discount) / 100;
    }
}

if (!function_exists('taxCalculation')) {
    function taxCalculation($price, $tax): float|int
    {
        return ($price * $tax) / 100;
    }
}

if (!function_exists('getImageUrl')) {
    function getImageUrl($file_path)
    {
        $diskName = config('filesystems.default') ?? 'public';

        if ($diskName === 'public') {
            // Local/public disk
            return config('app.url') . '/storage/' . ltrim($file_path, '/');
        }

        // For S3 or other cloud disks
        return Storage::disk($diskName)->url($file_path);
    }
}

if (!function_exists('getSellWithUsData')) {

    function getSellWithUsData(string $sectionSlug, string $key1, ?string $key2 = null)
    {
        $section = SellWithUsPage::where('section_slug', $sectionSlug)->first();

        if (!$section) {
            return null;
        }

        switch ($key1) {
            case 'title':
            case 'subtitle':
                return $section->$key1 ?? null;

            case 'data':
                if ($key2 === null) {
                    return processFileData($section->data ?? []);
                }

                foreach ($section->data as $item) {
                    if ($item['name'] === $key2) {
                        return $item['type'] === 'file' && ($item['value'] ?? false)
                            ? $item['value'] = getImageUrl($item['value'])
                            : ($item['value'] ?? null);
                    }
                }

                return null;

            case 'items':
                return processItemsData($section->items ?? []);

            default:
                return null;
        }
    }

    /**
     * Process file data in items array
     */
    function processItemsData(array $items): array
    {
        return array_map(function ($itemGroup) {
            $result = [];

            foreach ($itemGroup as $item) {
                $name = $item['name'] ?? null;
                $value = $item['value'] ?? null;

                if (($item['type'] ?? '') === 'file' && $value) {
                    $value = getImageUrl($value);
                }

                if ($name) {
                    $result[$name] = $value;
                }
            }

            return $result;
        }, $items);
    }

    /**
     * Process file data in data array
     */
    function processFileData(array $data): array
    {
        return array_map(function ($item) {
            if ($item['type'] === 'file' && ($item['value'] ?? false)) {
                $item['value'] = getImageUrl($item['value']);
            }

            return $item;
        }, $data);
    }
}

if (!function_exists('getInvoiceNo')) {
    function getInvoiceNo($table, $column, $prefix = 'ID', $length = 10)
    {
        $maxAttempts = 5;
        $batchSize = 20;

        do {
            $ids = collect(range(1, $batchSize))->map(function () use ($prefix, $length) {
                // Calculate the length of the random part
                $randomStringLength = $length - strlen($prefix);
                // Generate a truly random string
                $randomString = strtoupper(substr(bin2hex(random_bytes($randomStringLength)), 0, $randomStringLength));

                return $prefix . $randomString;
            });

            // Check which IDs already exist in the database
            $existingIds = DB::table($table)
                ->whereIn($column, $ids->toArray())
                ->pluck($column)
                ->toArray();

            // Filter out existing IDs
            $availableIds = $ids->diff($existingIds);

            // Use the first available ID
            if ($availableIds->isNotEmpty()) {
                $maxAttempts = 5;

                return $availableIds->first();
            }

            $maxAttempts--;
        } while ($maxAttempts > 0);

        throw new Exception('Unable to generate a unique ID after multiple attempts.' . $maxAttempts);
    }
}

if (!function_exists('userInfo')) {
    function userInfo(): ?User
    {
        $user = auth()->user();
        if (!$user) {
            throw new RuntimeException('No authenticated user found.');
        }

        return $user;
    }
}

if (!function_exists('customView')) {
    function customView($path = ['ajax' => '', 'default' => ''], $data = [])
    {
        $view = $path['default'];
        if (request()->ajax()) {
            $view = $path['ajax'];
        }

        return view($view, $data);
    }
}

if (!function_exists('variantText')) {
    function variantText($variations = [])
    {
        if (empty($variations)) {
            return null;
        }

        $text = '';
        foreach ($variations as $variation) {
            $text .= $variation->attribute->name . ': ' . $variation->attributeOption->attribute_value . ', ';
        }

        return rtrim($text, ', ');
    }
}

if (!function_exists('img')) {
    function img(
        ?string $path = null,
        ?string $placeholderText = null,
        int $width = 300,
        int $height = 150,
        string $bgColor = '#cccccc',
        string $textColor = '#666666'
    ): string {
        // Check if image exists
        $imageExists = $path && file_exists(public_path($path));

        // If image exists, return regular image URL
        if ($imageExists) {
            return asset($path);
        }

        // Generate placeholder SVG
        $placeholderSvg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">
                <rect width="100%%" height="100%%" fill="%s"/>
                <text x="50%%" y="50%%" fill="%s" font-family="Arial, sans-serif" font-size="16" text-anchor="middle" dominant-baseline="middle">
                    %s
                </text>
            </svg>',
            $width,
            $height,
            $width,
            $height,
            $bgColor,
            $textColor,
            htmlspecialchars($placeholderText ?? 'Image not found', ENT_QUOTES)
        );

        // Return base64 encoded SVG
        return 'data:image/svg+xml;base64,' . base64_encode($placeholderSvg);
    }
}

if (!function_exists('current_url')) {
    function current_url()
    {
        if (app()->environment('production')) {
            return secure_url(Request::path());
        }

        return Request::url();
    }
}

if (!function_exists('getModelChanges')) {
    /**
     * Get the old and new values of only the dirty attributes from an Eloquent model.
     *
     * @return array ['old' => [...], 'new' => [...]]
     */
    function getModelChanges(Model $model): array
    {
        $newData = $model->getDirty();                 // Only changed values (new)
        $original = $model->getOriginal();             // All original values
        $oldData = array_intersect_key($original, $newData); // Only old values of changed keys

        return [
            'old' => $oldData,
            'new' => $newData,
        ];
    }
}

if (!function_exists('banglaToBanglish')) {
    function banglaToBanglish($str)
    {
        if (!$str) {
            return '';
        }

        $loanwords = [
            'স্কুল' => 'school',
            'কলেজ' => 'college',
            'ইউনিভার্সিটি' => 'university',
            'বিমানবন্দর' => 'airport',
            'বিমান বন্দর' => 'biman-bandar',
            'ক্যান্টনমেন্ট' => 'cantonment',
            'নিউ মার্কেট' => 'new-market',
            'শেরে বাংলা নগর' => 'sher-e-bangla-nagar',
            'ঢাকা' => 'dhaka',
            'হাসপাতাল' => 'hospital',
            'মার্কেট' => 'market',
            'প্লাজা' => 'plaza',
            'সেন্টার' => 'center',
            'কমপ্লেক্স' => 'complex',
            'ফিউচার পার্ক' => 'future-park',
            'সিটি' => 'city',
            'ট্রেড সেন্টার' => 'trade-center',
            'শপিং মল' => 'shopping-mall',
            'রেলওয়ে' => 'railway',
            'স্টেশন' => 'station',
            'টার্মিনাল' => 'terminal',
            'জাদুঘর' => 'museum',
            'উদ্যান' => 'garden',
            'পার্ক' => 'park',
            'কেল্লা' => 'fort',
            'সংসদ ভবন' => 'parliament-house',
            'চিড়িয়াখানা' => 'zoo',
            'শিল্পকলা একাডেমি' => 'shilpakala-academy',
            'মুক্তিযুদ্ধ' => 'liberation-war',
            'ক্লাব' => 'club',
            'হোটেল' => 'hotel',
            'রেস্টুরেন্ট' => 'restaurant',
            'কাবাব' => 'kabab',
            'লেক' => 'lake',
            'সার্কেল' => 'circle',
            'রোড' => 'road',
            'স্টেডিয়াম' => 'stadium',
            'মসজিদ' => 'mosque',
            'স্মৃতিসৌধ' => 'memorial',
            'ক্যাম্প' => 'camp',
            'বাস স্ট্যান্ড' => 'bus-stand',
            // Add more as needed for specific cases
        ];

        // Check if the entire string matches a loanword
        if (isset($loanwords[$str])) {
            return $loanwords[$str];
        }

        // Character and conjunct mapping
        $bangla_map = [
            // Vowels
            'অ' => '', // Implicit vowel, handled contextually
            'আ' => 'a',
            'ই' => 'i',
            'ঈ' => 'ee',
            'উ' => 'u',
            'ঊ' => 'oo',
            'ঋ' => 'ri',
            'এ' => 'e',
            'ঐ' => 'oi',
            'ও' => 'o',
            'ঔ' => 'ou',
            // Consonants
            'ক' => 'k',
            'খ' => 'kh',
            'গ' => 'g',
            'ঘ' => 'gh',
            'ঙ' => 'ng',
            'চ' => 'ch',
            'ছ' => 'chh',
            'জ' => 'j',
            'ঝ' => 'jh',
            'ঞ' => 'n',
            'ট' => 't',
            'ঠ' => 'th',
            'ড' => 'd',
            'ঢ' => 'dh',
            'ণ' => 'n',
            'ত' => 't',
            'থ' => 'th',
            'দ' => 'd',
            'ধ' => 'dh',
            'ন' => 'n',
            'প' => 'p',
            'ফ' => 'ph',
            'ব' => 'b',
            'ভ' => 'bh',
            'ম' => 'm',
            'য' => 'y',
            'র' => 'r',
            'ল' => 'l',
            'শ' => 'sh',
            'ষ' => 'sh',
            'স' => 's',
            'হ' => 'h',
            'ড়' => 'r',
            'ঢ়' => 'rh',
            'য়' => 'y',
            // Common conjuncts
            'ক্ক' => 'kk',
            'ক্ট' => 'kt',
            'ক্ত' => 'kt',
            'ক্ষ' => 'ksh', // Adjusted for better fit (e.g., খিলক্ষেত → khilkhet)
            'গ্ন' => 'gn',
            'গ্গ' => 'gg',
            'ঙ্গ' => 'ng', // For গঙ্গা, গুলশান
            'ঙ্ক' => 'nk',
            'চ্চ' => 'cch',
            'জ্জ' => 'jj',
            'জ্ঞ' => 'gy',
            'ট্ট' => 'tt',
            'ড্ড' => 'dd',
            'ণ্ড' => 'nd', // For ধানমন্ডি → dhanmondi
            'ত্ত' => 'tt',
            'দ্দ' => 'dd',
            'দ্ধ' => 'ddh',
            'ন্দ' => 'nd',
            'ন্ন' => 'nn',
            'ন্ম' => 'nm',
            'ন্স' => 'ns', // For বনশ্রী → banasree
            'প্প' => 'pp',
            'ব্দ' => 'bd',
            'ব্ধ' => 'bdh',
            'ম্প' => 'mp',
            'ম্ভ' => 'mbh',
            'ল্ল' => 'll',
            'শ্চ' => 'shch',
            'ষ্ট' => 'sht',
            'স্ক' => 'sk',
            'স্প' => 'sp',
            'স্ত' => 'st',
            'স্ম' => 'sm',
            'স্ব' => 'sw',
            // Vowel signs
            'া' => 'a',
            'ি' => 'i',
            'ী' => 'ee',
            'ু' => 'u',
            'ূ' => 'oo',
            'ৃ' => 'ri',
            'ে' => 'e',
            'ৈ' => 'oi',
            'ো' => 'o',
            'ৌ' => 'ou',
            // Other symbols
            'ং' => 'ng',
            'ঃ' => 'h',
            'ঁ' => 'n',
            '।' => '.',
            ',' => ',',
            ' ' => ' ',
            // Numbers
            '০' => '0',
            '১' => '1',
            '২' => '2',
            '৩' => '3',
            '৪' => '4',
            '৫' => '5',
            '৬' => '6',
            '৭' => '7',
            '৮' => '8',
            '৯' => '9',
            '্' => '', // Virama (halant)
        ];

        // Replace loanwords first (for multi-word phrases)
        foreach ($loanwords as $bangla => $english) {
            $str = str_replace($bangla, $english, $str);
        }

        // Split the string into characters to handle conjuncts and vowels
        $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        $result = '';
        $i = 0;
        $length = count($chars);

        while ($i < $length) {
            $current = $chars[$i];
            $next = ($i + 1 < $length) ? $chars[$i + 1] : '';
            $next2 = ($i + 2 < $length) ? $chars[$i + 2] : '';

            // Check for conjuncts (three-character combinations, e.g., ক্ষ = ক + ্ + ষ)
            if ($next === '্' && $next2 !== '') {
                $conjunct = $current . $next . $next2;
                if (isset($bangla_map[$conjunct])) {
                    $result .= $bangla_map[$conjunct];
                    $i += 3;

                    continue;
                }
            }

            // Check for two-character conjuncts (e.g., ণ্ড, ত্ত)
            if ($next !== '' && isset($bangla_map[$current . $next])) {
                $result .= $bangla_map[$current . $next];
                $i += 2;

                continue;
            }

            // Handle standalone characters
            if (isset($bangla_map[$current])) {
                // Special handling for consonants without explicit vowels
                if (
                    in_array($current, ['ক', 'খ', 'গ', 'ঘ', 'ঙ', 'চ', 'ছ', 'জ', 'ঝ', 'ঞ', 'ট', 'ঠ', 'ড', 'ঢ', 'ণ', 'ত', 'থ', 'দ', 'ধ', 'ন', 'প', 'ফ', 'ব', 'ভ', 'ম', 'য', 'র', 'ল', 'শ', 'ষ', 'স', 'হ', 'ড়', 'ঢ়', 'য়']) &&
                    ($i + 1 >= $length || $chars[$i + 1] !== 'া' && !in_array($chars[$i + 1], ['ি', 'ী', 'ু', 'ূ', 'ৃ', 'ে', 'ৈ', 'ো', 'ৌ', 'ং', 'ঃ', 'ঁ']))
                ) {
                    $result .= $bangla_map[$current];
                } else {
                    $result .= $bangla_map[$current];
                }
            } else {
                $result .= $current; // Keep unmapped characters as-is
            }
            $i++;
        }

        // Post-processing: Clean up and format as slug
        // Remove extra spaces and convert to lowercase
        $result = strtolower(trim($result));

        // Replace spaces with hyphens for slugs
        $result = preg_replace('/\s+/', '-', $result);

        // Remove redundant hyphens
        $result = preg_replace('/-+/', '-', $result);

        // Trim hyphens from start and end
        $result = trim($result, '-');

        // Specific fixes for known discrepancies
        $partial_fixes = [
            'mandi' => 'mondi',
            'shaho' => 'shah',
            'mti' => 'moti',
            'khilkhet' => 'khilkhet',
            'banshri' => 'banasree',
            'oya' => 'wa',
            'haus' => 'house',
            'eya' => 'ea',
            'phly' => 'fl',
            'byang' => 'ban',
            'etiem' => 'atm',
            'jenarel' => 'general',
            'mdel' => 'model',
        ];

        foreach ($partial_fixes as $find => $replace) {
            if (strpos($result, $find) !== false) {
                $result = str_replace($find, $replace, $result);
            }
        }

        return $result;
    }
}

if (!function_exists('start_date')) {
    function start_date($date = null)
    {
        if ($date) {
            return Carbon::parse($date)->startOfDay();
        }

        return null;
    }
}

if (!function_exists('end_date')) {
    function end_date($date = null)
    {
        if ($date) {
            return Carbon::parse($date)->endOfDay();
        }

        return null;
    }
}

if (!function_exists('discount_price')) {
    function discount_price($productId, $regular_price, $discount_price): float
    {
        $campaignProduct = CampaignProduct::where('product_id', $productId)->first();

        // If this product is not in any campaign → return regular price
        if (!$campaignProduct) {
            return $discount_price;
        }

        $campaign = $campaignProduct->campaign;

        // If campaign is missing or invalid → return regular price
        if (!$campaign || !$campaign->starts_at || !$campaign->ends_at) {
            return $discount_price;
        }

        $start = Carbon::parse($campaign->starts_at);
        $end = Carbon::parse($campaign->ends_at);
        $now = Carbon::now();

        // Campaign not active → return regular price
        if (!$now->between($start, $end)) {
            return $discount_price;
        }

        // Fetch discount info from prime view pivot
        $primeView = $campaign->campaignPrimeViews()
            ->where('prime_view_id', $campaignProduct->prime_view_id)
            ->first();
        // No discount configured → return regular price
        if (!$primeView) {
            return $discount_price;
        }

        $discountAmount = $primeView->discount_amount;
        $discountType = $primeView->discount_type;

        // Apply discount
        if ($discountType == DiscountTypes::PERCENTAGE->value) {
            $final = $regular_price - (($regular_price * $discountAmount) / 100);
        } else { // fixed discount
            $final = $regular_price - $discountAmount;
        }

        return max($final, 0);
    }
}

if (!function_exists('carbon')) {
    function carbon($time = null, $tz = null)
    {
        return Carbon::parse($time, $tz);
    }
}

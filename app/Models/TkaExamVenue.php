<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TkaExamVenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'province',
        'postal_code',
        'contact_person',
        'contact_phone',
        'contact_email',
        'capacity',
        'facilities',
        'is_active'
    ];

    protected $casts = [
        'facilities' => 'array',
        'is_active' => 'boolean',
        'capacity' => 'integer'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByProvince($query, $province)
    {
        return $query->where('province', $province);
    }

    public function scopeWithCapacity($query, $minCapacity)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }

    public function scopeWithFacility($query, $facility)
    {
        return $query->whereJsonContains('facilities', $facility);
    }

    // Relationships
    public function schedules()
    {
        return $this->hasMany(TkaSchedule::class, 'exam_venue', 'name');
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->city . ', ' . $this->province . ' ' . $this->postal_code;
    }

    public function getFormattedCapacityAttribute()
    {
        return number_format($this->capacity) . ' peserta';
    }

    public function getFacilitiesListAttribute()
    {
        if (!$this->facilities) {
            return [];
        }
        return $this->facilities;
    }

    public function getFacilitiesStringAttribute()
    {
        if (!$this->facilities) {
            return 'Tidak ada fasilitas';
        }
        return implode(', ', $this->facilities);
    }

    public function getContactInfoAttribute()
    {
        return [
            'person' => $this->contact_person,
            'phone' => $this->contact_phone,
            'email' => $this->contact_email
        ];
    }

    public function getFormattedContactAttribute()
    {
        $info = [];
        if ($this->contact_person) {
            $info[] = $this->contact_person;
        }
        if ($this->contact_phone) {
            $info[] = 'Telp: ' . $this->contact_phone;
        }
        if ($this->contact_email) {
            $info[] = 'Email: ' . $this->contact_email;
        }
        return implode(' | ', $info);
    }

    // Methods
    public function hasFacility($facility)
    {
        if (!$this->facilities) {
            return false;
        }
        return in_array($facility, $this->facilities);
    }

    public function addFacility($facility)
    {
        if (!$this->facilities) {
            $this->facilities = [];
        }
        
        if (!in_array($facility, $this->facilities)) {
            $facilities = $this->facilities;
            $facilities[] = $facility;
            $this->facilities = $facilities;
            $this->save();
        }
    }

    public function removeFacility($facility)
    {
        if (!$this->facilities) {
            return;
        }
        
        $facilities = array_filter($this->facilities, function($f) use ($facility) {
            return $f !== $facility;
        });
        
        $this->facilities = array_values($facilities);
        $this->save();
    }

    public function getUpcomingSchedules()
    {
        return $this->schedules()
                    ->where('start_date', '>', now())
                    ->where('is_active', true)
                    ->orderBy('start_date')
                    ->get();
    }

    public function getOngoingSchedules()
    {
        return $this->schedules()
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->where('is_active', true)
                    ->get();
    }

    public function getCompletedSchedules()
    {
        return $this->schedules()
                    ->where('end_date', '<', now())
                    ->where('is_active', true)
                    ->orderBy('end_date', 'desc')
                    ->get();
    }

    public function getTotalSchedules()
    {
        return $this->schedules()->count();
    }

    public function getUpcomingSchedulesCount()
    {
        return $this->getUpcomingSchedules()->count();
    }

    public function isAvailable($startDate, $endDate)
    {
        $conflictingSchedules = $this->schedules()
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->where('is_active', true)
            ->count();

        return $conflictingSchedules === 0;
    }

    public function getUtilizationRate()
    {
        $totalCapacity = $this->capacity;
        if ($totalCapacity <= 0) {
            return 0;
        }

        // This would need to be calculated based on actual registrations
        // For now, return a placeholder
        return 0;
    }

    public function isFullyBooked()
    {
        return $this->getUtilizationRate() >= 100;
    }

    public function getAvailableCapacity()
    {
        $utilizationRate = $this->getUtilizationRate();
        return $this->capacity - ($this->capacity * $utilizationRate / 100);
    }
}

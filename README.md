# Positives Thoughts:
- Overall project code health is good.
- Naming convention, coding convention is better as it uses repository. (ignoring spelling mistake, that's fixed in refactoring noramlJobs => normalJobs)

  Repository File:
    - This inclusion is better
    - By moving business logic in this file is better for readability
  Controller File:
    - Controller is skinny, as our business logic is moved to the repository

# Optional Improvement: 
I created an interface that was implemented in BookingRepository, giving a concept, in any scenario when Data Source /DB is not compatible with ORM,
we can make another Repo that implements this interface and helps define compatible functions according to this Data source.
I used three functions as examples. more can be added and interface can be used as a template for the same repository with Different Data Source.
getUsersJobs
getUsersJobsHistory
getPotentialJobIdsWithUserId

_______________________________________
# Improvements:
Reducing Repetition and improving readability

By defining a function resolveUserType, it can be reused and avoid declaring $usertype in if else.
$usertype = $this->resolveUserType($cuser);


     $job_type = 'unpaid';
        $translator_type = $cuser_meta->translator_type;
        if ($translator_type == 'professional')
            $job_type = 'paid';   /*show all jobs for professionals.*/
        else if ($translator_type == 'rwstranslator')
            $job_type = 'rws';  /* for rwstranslator only show rws jobs. */
        else if ($translator_type == 'volunteer')
            $job_type = 'unpaid';  /* for volunteers only show unpaid jobs. */

can be converted to , after defining function resolveJobType in refactoring

 $translator_type = $cuser_meta->translator_type;
$job_type = $this->resolveJobType($translator_type);
        
___________________________________



if (isset($page)) {
            $pagenum = $page;
        } else {
            $pagenum = "1";
 }
above code can be written this way using the ternary operator and $request->has
 $pagenum = $request->has('page') ? $request->page: "1";
___________________________________
$immediatetime =5;
should move to constant and called as self::IMMEDIATE_TIME, easy to modify and reuse
Similarly more constants can be used to improve reusability

          $response['message'] = "Du måste fylla in alla fält";
  $response['message'] = self::FALT_STRING;

const DEFAULT_PAGE_SIZE = 15;
->paginate(15) should be ->paginate(self::DEFAULT_PAGE_SIZE);


const EMAIL_SUBJECT = "Information om avslutad tolkning för bokningsnummer #";

$subject = self::EMAIL_SUBJECT . $job->id;

___________________________________

       return compact('emergencyJobs', 'normalJobs', 'cuser', 'usertype');
//        return ['emergencyJobs' => $emergencyJobs, 'normalJobs' => $normalJobs, 'cuser' => $cuser, 'usertype' => $usertype];
using compact can reduce code and better readable, wheere possible.
____________________________________
eloquent query builder should be used to leverage when instead of using if, else, for better readability

$allJobs->when($requestdata['feedback'] && $requestdata['feedback'] != 'false', function ($qry) {
                return $qry->where('ignore_feedback', '0')
                    ->whereHas('feedback', function ($q) {
                    $q->where('rating', '<=', '3');
                });
            });
_____________________________________________
($not_get_notification == 'yes') return false;
 return true;

==> 
 return !$not_get_notification == 'yes';


# Overall Improvements:

  Controller File:
    - [Major] When empty objects, is better to respond with proper response code (i.e. 400, 404,)
			also when created, 201

Wherever, repository is returning status, we can use ternarary operator to 
set appropriat status
 $code = $response['status'] == 'success' ? 201 : 400;

    - [Minor] Many endpoints can be reduced to one, by doing params
              Accept job, cancel job, end job


_________________________________
# Terrible things

querying in foreach should be avoided, instead extend query with filters, when querying data at first time

e.g, Job::checkParticularJob($user_id, $item); where possible, need to be used filter when querying first time.

Emails/ SMS/ OneSignal notifications codes should be shifted to listeners, and bound to specific events. Event should be fired to trigger this.
this will help cleaner code, reusability, and easily queable when needed.


try catch should be used so any failed iteration shouldnt effect the loop.

try {
                            $job_for_translator = Job::assignedToPaticularTranslator($userId, $oneJob->id);
                            if ($job_for_translator == 'SpecificJob') {
                                $job_checker = Job::checkParticularJob($userId, $oneJob);
                                if (($job_checker != 'userCanNotAcceptJob')) {
                                    if ($this->isNeedToDelayPush($oneUser->id)) {
                                        $delpay_translator_array[] = $oneUser;
                                    } else {
                                        $translator_array[] = $oneUser;
                                    }
                                }
                            }
                        }catch (\Exception $exception){
                            Log::error($exception->getMessage());
                        }


  try {
                $this->mailer->send($email, $name, $subject, 'emails.session-ended', $dataEmail);
            } catch(\Exception $e) {
                Log::error($e->getMessage());
            }

added in 'emails.session-ended', but can be added to all emails.

Try catch should be used in controller as well.

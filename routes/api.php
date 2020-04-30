<?php

use Illuminate\Http\Request;
use App\Models\Activity;
use  App\Models\Period;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::resource('period', 'PeriodController');
Route::resource('activity', 'ActivityController');
Route::resource('grade', 'GradeController');
Route::resource('student', "StudentsController");

Route::post("/getActivitySchoolClassBranchStudent", "StudentsController@aschollClassBranchStudents");
Route::post("/getSchoolClassBranchStudent", "StudentsController@schollClassBranchStudents");
Route::post("/getStudentsDiscont", "StudentsController@getStudentsDiscont");
Route::post("/getAStudentsDiscont", "StudentsController@getAStudentsDiscont");
//Route::resource('file', "FileController");

Route::post("/updatefile/{id}", "FileController@update");


Route::post("test", "TestController@test");


Route::get("actwithperiods", "ActivityController@withperiods");
Route::post("uniqaperiods", "ActivityController@uniqaperiods");
// uniqaperiods

Route::post("saveimage", 'StudentsController@saveimage');


/* Discont */
Route::post("/getActivityPerCls", "ActivityController@activityPeriodClass");
Route::get("/activityWithPeriods/{id}", "ActivityController@activityWithPeriods");
Route::post("/schooldstatusstudent", "DiscontinuityController@SchoolStatusDiscountStudents");
Route::post("/schooldastatusstudent", "DiscontinuityController@ActivityStatusDiscountStudents");
Route::post("/studentDiscont", "DiscontinuityController@studentDiscont");
Route::post("/studentDiscontA", "DiscontinuityController@studentDiscontA");
Route::post("/getAllStudents", "StudentsController@getAllStudents");
Route::post("/createDiscont", "DiscontinuityController@store");
Route::post("/getDiscont", "DiscontinuityController@index");
Route::post("/deleteDiscont", "DiscontinuityController@delete");
Route::post("/updateDiscont", "DiscontinuityController@update");
/* Discont */

//
Route::resource('actperpivot', 'ActPerPivotController');
Route::get('getaplist', 'ActPerPivotController@getAPList');
Route::post('deleteactivityperiod', 'ActPerPivotController@deleteActPer');
Route::post('actperstoremultiple', 'ActPerPivotController@storemultiple');
Route::post('others', 'ActPerPivotController@others');
Route::post("actperdelother", "ActPerPivotController@delActPerOther");
Route::post("actperothers", 'ActPerPivotController@actperothers');
Route::post("actperlessons", 'ActPerPivotController@aplessons');
Route::post("addactperlesson", 'ActPerPivotController@addActPerLesson');
Route::post("deleteactperlesson", 'ActPerPivotController@deleteActPerLesson');


Route::put("/updatestudentstatus/{id}", "StudentsController@updateStudentStatus");


Route::post('students', "StudentsController@getStudents");
Route::post('studentlist', "StudentsUsersParentsController@studentlist");
Route::post('assignstudentlist', "StudentsUsersParentsController@assignstudentlist");
Route::post('assignstudent', "AssignStudentPersonController@assignstudent");
Route::post('assignperson', "AssignStudentPersonController@assignperson");
Route::post('employeelist', "StudentsUsersParentsController@employeelist");
Route::post('parentlist', "StudentsUsersParentsController@parentlist");
Route::post('filterdetailsearch', "StudentsUsersParentsController@filterDetailSearch");
//student relation
Route::post("createstudentactivity", 'StudentRelationController@createActivity');
Route::post("getstudentactivities", 'StudentRelationController@getActivities');
Route::post("delstudentactivity", 'StudentRelationController@deleteActivity');
Route::post("delstudentclub", 'StudentRelationController@deleteClub');

Route::post("createstudentschool", 'StudentRelationController@createSchool');
Route::post("createstudentclub", 'StudentRelationController@createClub');
Route::post("getstudentclubs", 'StudentRelationController@getStdClubs');
Route::post("getstudentschool", 'StudentRelationController@getSchool');
Route::post("delstudentschool", 'StudentRelationController@deleteSchool');

Route::post("createstudentdetails", 'StudentRelationController@createDetails');
Route::get("getstudentdetails", 'StudentRelationController@getDetails');

// Route::post("createstudentusers", 'StudentRelationController@createUsers');
Route::get("getstudentusers", 'StudentRelationController@getUsers');
Route::post("getclubs", 'StudentRelationController@getClubs');

//adem


Route::post('schoolothers', 'SchoolClasesBranchesPivotController@others');
Route::post('studentlessons', 'StudentLessonRelationController@getstudentlessons');
Route::post('optinallessons', 'StudentLessonRelationController@getoptinallessons');
Route::post('createstudentlessons', 'StudentLessonRelationController@createStudentLessons');
Route::post('deletestudentlessons', 'StudentLessonRelationController@deleteStudentLessons');
Route::post('deletestudentmultilessons', 'StudentLessonRelationController@deleteStudentMultiLessons');


Route::post('schoolstudentdifflessons', 'StudentLessonRelationController@getStdSchoolDiffLessons');


Route::post('activitystudentdifflessons', 'StudentLessonRelationController@getStdActivityDiffLessons');

Route::post('getstudentaplessons', 'StudentLessonRelationController@getStudentAPlessons');


// Route::post("actperothers", 'ActPerPivotController@others');


//test
//student
Route::post('getstudentsorusers', 'StudentUserController@getUsersOrStudents');
Route::post('createstudentuser', 'StudentUserController@store');
Route::post('assignstudentuser', 'StudentUserController@assignUserStudent');
Route::post('deletestudentuser', 'StudentUserController@deleteUserStudent');
Route::post('updatestudentuser', 'StudentUserController@update');
Route::post('saveuserimage', 'StudentUserController@saveimage');
Route::post('searchusers', 'StudentUserController@searchUsers');
//user


/* DTYPE */

Route::post("/getDtype", "DTypeController@index");
Route::post("/getAllDtypePagination", "DTypeController@getall");
Route::post("/createDType", "DTypeController@store");
Route::delete("/deleteDType/{id}", "DTypeController@destroy");
Route::put("/updateDType/{id}", "DTypeController@update");
/* DTYPE */
//

Route::get('ptypes', 'PTypeController@getPTypes');
Route::post('createactivityptype', 'ActivityPTypePivotController@create');
Route::delete('deleteactivityptype/{id}', 'ActivityPTypePivotController@destroy');
Route::post('getactivityptype', 'ActivityPTypePivotController@getActivityPTypes');
Route::post('createactivitypdays', 'ActivityDayController@create');
Route::post('getactivitypdays', 'ActivityDayController@getActivityPDays');
Route::delete('deleteactivitypdays/{id}', 'ActivityDayController@destroy');
//hour
Route::post('createactivityphours', 'ActivityHourController@create');
Route::post('getactivityphours', 'ActivityHourController@getActivityPHours');
Route::delete('deleteactivityphours/{id}', 'ActivityHourController@destroy');
//program
Route::post('getactivitypgrades', 'ActivityProgramController@getGrades');
Route::post('getactivityplessons', 'ActivityProgramController@getLessons');
Route::post('getactivitypteachers', 'ActivityProgramController@getTeachers');
//day
Route::post('createclubpdays', 'ClubDayController@create');
Route::post('getclubpdays', 'ClubDayController@getClubPDays');
Route::delete('deleteclubpdays/{id}', 'ClubDayController@destroy');
//hour
Route::post('createclubphours', 'ClubHourController@create');
Route::post('getclubphours', 'ClubHourController@getClubPHours');
Route::delete('deleteclubphours/{id}', 'ClubHourController@destroy');
//program
Route::post('getclubpgrades', 'ClubProgramController@getGrades');
Route::post('getclubplessons', 'ClubProgramController@getLessons');
Route::post('getclubpteachers', 'ClubProgramController@getTeachers');


Route::post('getclubpcontents', 'ClubProgramContentController@getContents');

//spor club
Route::post('createclubptype', 'ClubPTypePivotController@create');
Route::post('getclubptype', 'ClubPTypePivotController@getClubPTypes');
Route::delete('deleteclubptype/{id}', 'ClubPTypePivotController@destroy');
Route::post('createclubpcontents', 'ClubProgramContentController@create');
Route::delete('deleteclubpcontent/{id}', 'ClubProgramContentController@destroy');

Route::post('getcpscheduleteachers', 'ClubProgramContentController@getClubScheduleTeachers');
Route::post('getcpteachercontents', 'ClubProgramContentController@getTeacherContents');
//spor club

//Yasin
Route::post("/getSporClubUserProgram", "ClubProgramContentController@getSporClubUserProgram");

//Yasin

//yasin
Route::post("/getAcitivityUserProgram", "ActivityProgramContentController@getActivitUserProgram");
Route::post("/getTodayActivityUserProgram", "ActivityProgramContentController@getTodayActivitUserProgram");
//yasin

Route::post('getactivitypcontents', 'ActivityProgramContentController@getContents');

Route::post('getschoolpcontents', 'ActivityProgramContentController@getSchoolContents');
Route::post('getapscheduleteachers', 'ActivityProgramContentController@getActScheduleTeachers');
Route::post('getapteachercontents', 'ActivityProgramContentController@getTeacherContents');
Route::post('createactivitypcontents', 'ActivityProgramContentController@create');
Route::delete('deleteactivitypcontent/{id}', 'ActivityProgramContentController@destroy');
//


/* Wish*/
Route::post("/wish", "WishController@store");
/* Wish*/
/*Files*/
Route::post("/files", "FileController@store");
Route::post("/testet", "FileController@test");
Route::post("/addmultiplefiles", "FileController@addmultiplefile");

/*Files*/


/*Users*/
Route::get("/getSporClubUserProgram/{id}", "UserController@getSporClubProgram");
Route::get("/getactivityUserProgram/{id}", "UserController@getActivityProgram");
Route::get("/getUserProgram/{id}", "UserController@getProgram");
Route::post("/getUserProgram", "UserController@getSchoolProgram");
Route::post("/getPersonStudents", "UserController@getStudents");
Route::post("/users", "UserController@store");
Route::get("/user/{id}", "UserController@show");
Route::post("/getuserschool/{id}", "UserController@getSchool");
Route::post("/getuserlesson/{id}", "UserController@getLesson");
Route::post("/getuserschoolclases", "UserController@getSchoolClases");
Route::post("/getuserschoolclasesbranches", "UserController@getSchoolClasesBranches");
Route::post("/getPersons", "UserController@getPersons");
Route::post("/getAllPersonsExportExcel", "UserController@getAllPersonsExportExcel");
Route::delete('users/{id}', "UserController@destroy");
Route::post("/getUserSchoolLessons", "UserController@getSchoolLessons");
Route::put("/updatePersons/{id}", "UserController@updatePersons");
Route::put("/updateUserImage/{id}", "UserController@updatePersonImg");
Route::post("/getUserSporClub/{id}", "UserController@getSporClub");
Route::post("/getUserSporClubTeam", "UserController@getSporClubTeam");
Route::post("/getAllUsers", "UserController@getAllUsers");
Route::post("/getAllStudentAndUsers", "UserController@getAllStudentAndUsers");
Route::post("/getuserwithlesson", "UserController@getUserWithLesson");
/*User*/

/*UserActıvıty*/
Route::get("/getAllActivityUserRelation", "UserActivityRelationController@getAll");
Route::post("/getActivityInfo", "UserActivityRelationController@getActivityInfo");
Route::post("/getActivityWeekLesson", "UserActivityRelationController@getActivityWeekLesson");
/*UserActıvıty*/

/* UserPassword */
Route::post("/userpassword", "UsersPasswordsController@store");
/* UserPassword */

/*Users_Types*/


/*Users_Types*/


/*Proximities*/

Route::get("proximities", 'StudentRelationController@proximities');
/*Proximities*/


/*UserUTypes*/
Route::post("/userutype", "UserUTypesController@store");

/*UserUTypes */


/*UserSchool*/
Route::post("/userschool", "UsersSchoolsController@store");
Route::post("/deleteuserschool", "UsersSchoolsController@delete");

/*UserSchool*/

/*UserLessons*/
Route::post("/userlessons", "UsersLessonsController@store");

/*UserSchoolLessons*/
Route::post("/userschoollessons", "UsersSchoolsLessonsController@store");
Route::delete("/userschoollessons/{id}", "UsersSchoolsLessonsController@destroy");
Route::post("/getUserSchoolLesson", "UsersSchoolsLessonsController@getSchoolUserLesson");
/*UserSchoolLessons */

/*UserSchoolClases*/
Route::post("/userschoolclases", "UsersSchoolsClasesController@store");
Route::post("/deleteuserschoolclases/{id}", "UsersSchoolsClasesController@delete");
/*UserSchoolClases*/

/*UserSchoolClasesBranches*/
Route::post("/userschoolclasesbranches", "UserSchoolClasesBranchesController@store");

Route::delete('userschoolclasesbranches/{id}', "UserSchoolClasesBranchesController@destroy");


/*UserSchoolClasesBranches*/

/*UserSporClubTeam*/
Route::post("/addUserSporClubTeam", "UserSporClubTeamBranchController@store");
Route::post("/deleteUserSporClubTeam", "UserSporClubTeamBranchController@delete");
/*UserSporClubTeam*/

/*UserSporClub*/
Route::post("/addUserSporClub", "UserSporClubController@store");
Route::post("/deleteUserSporClub", "UserSporClubController@delete");
/*UserSporClub*/

/* Province */
Route::post('/getProvinces', 'ProvinceController@getProvinces');
Route::post('/province', 'ProvinceController@store');
Route::get('/province/{id}', 'ProvinceController@show');

Route::get("/getAllProvince", "ProvinceController@getAllProvince");

Route::put('/province/{id}', 'ProvinceController@update');
Route::delete('/province/{id}', 'ProvinceController@destroy');
/* Province */

/* Companies*/

Route::post('/getCompanies', 'CompaniesController@getCompanies');
Route::post('/companies', 'CompaniesController@store');
Route::get('/companies/{id}', 'CompaniesController@show');
Route::put('/companies/{id}', 'CompaniesController@update');
Route::delete('/companies/{id}', 'CompaniesController@destroy');
Route::get("/getAllCompanies", 'CompaniesController@getAllCompanies');

/* Companies */


/*Teams*/
Route::post("/getTeams", "TeamController@index");
Route::post("/addTeams", "TeamController@store");
Route::put("/updateTeams/{id}", "TeamController@update");
Route::delete("/deleteTeams/{id}", "TeamController@delete");
Route::get("/getallTeams", "TeamController@getall");
/*Teams*/

/*SporClub Branches */
Route::post("/addSporClubBranch", "SporClubBranchController@store");
Route::post("/getSporClubBranch", "SporClubBranchController@index");
Route::delete("/deleteSporClubBranch/{id}", "SporClubBranchController@delete");
Route::put("/updateSporClubBranch/{id}", "SporClubBranchController@update");
Route::get("/getAllSporClubBranch", "SporClubBranchController@getall");
/*SporClub Branches */


/*SporClubTeamBranch*/
Route::post("/deleteSCTeamBranchStudent", "SporClubTeamBranchController@deleteStudents");
Route::post("/addSCTeamBranch", "SporClubTeamBranchController@store");
Route::post("/getSCTeamBranch", "SporClubTeamBranchController@index");
Route::post("/deleteSCTeamBranch/{id}", "SporClubTeamBranchController@delete");
Route::get("/getAllExport/{id}", "SporClubTeamBranchController@allExport");
Route::post("/getAllSCTeam", "SporClubTeamBranchController@getAll");

/*SporClubTeamBranch*/


/*Spor Club*/
Route::post("/getSporClubs", "SporClubController@getSporClubs");
Route::delete("/sporclub/{id}", "SporClubController@delete");
Route::post("/addsporClub", "SporClubController@store");
Route::put("/updateSporClub/{id}", "SporClubController@update");
Route::get("/getAllSporclub", "SporClubController@getall");
Route::get("/showSporClub/{id}", "SporClubController@show");
Route::post("/getSporClubUser/{id}", "SporClubController@getUser");
Route::post("/getSporClubUserExport/{id}", "SporClubController@getUserExport");
Route::post("/getSporClubStudents", "SporClubController@getStudents");
Route::post("/getAllStudentsExport", "SporClubController@getAllExport");
/*Spor Club*/

/*Clases*/
Route::post('/getClases', 'ClasesController@getClases');
Route::post('/clases', 'ClasesController@store');
Route::get('/clases/{id}', 'ClasesController@show');
Route::put('/clases/{id}', 'ClasesController@update');
Route::delete('/clases/{id}', 'ClasesController@destroy');
Route::get("/getAllClases", "ClasesController@getAllClases");
/*Clases*/

/* Titles */
Route::post('/getTitles', 'TitleController@getTitles');
Route::post('/title', 'TitleController@store');
Route::get('/title/{id}', 'TitleController@show');
Route::put('/title/{id}', 'TitleController@update');
Route::delete('/title/{id}', 'TitleController@destroy');

Route::get("/getAlltitles", "TitleController@getAlltitles");

/* Titles */

/*Schools */
Route::get("/showSchool/{id}", "SchoolsController@show");
Route::post('/getSchools', 'SchoolsController@getSchools');
Route::post('/school', 'SchoolsController@store');
Route::delete('/school/{id}', 'SchoolsController@destroy');
Route::put('/school/{id}', 'SchoolsController@update');
Route::post("/getschooluser/{id}", "SchoolsController@getUser");
Route::post("/getAllSchoolUser/{id}", "SchoolsController@getAllSchoolUser");
Route::get("/getAllSchool", "SchoolsController@getAllSchool");
Route::post("/getSchoolStudents", "SchoolsController@getStudents");
Route::post("/getExcelStudentsData", "SchoolsController@getAllStudentExportExcel");

/*Schools */

/*SchoolProgram*/
Route::post("/test11", "SchoolProgramController@test");
Route::post("/getUserSchoolProgramToday", "SchoolProgramController@getUserSchoolProgramToday");
Route::post("/getUserSchoolProgram", "SchoolProgramController@getUserSchoolProgram");
Route::post("/getSchoolClassProgram", "SchoolProgramController@getSchoolProgram");
Route::post("/addSchoolProgram", "SchoolProgramController@store");
Route::post("/deleteSchoolProgram", "SchoolProgramController@delete");
Route::post("/updateSchoolProgram", "SchoolProgramController@update");
/*SchoolProgram*/

/*School ProgramType*/
Route::delete("/deleteSchoolProgramType/{id}", "SchoolPTypeController@destroy");
Route::post("/addSchoolProgramType", "SchoolPTypeController@store");
Route::get("/getAllSchoolProgramTypes", "SchoolPTypeController@getAll");
Route::get("/showSchoolProgramType/{id}", "SchoolPTypeController@show");
/*School ProgramType*/


/*SchoolProgramDays*/
Route::post("/addSchoolProgramDays", "SchoolDayController@store");
Route::post("/getSchoolDays", "SchoolDayController@index");
Route::post("/deleteSchoolDays", "SchoolDayController@delete");

/*SchoolProgramDays*/

/*SchoolProgramHours*/
Route::post("/addSchoolProgramHour", "SchoolHourController@store");
Route::post("/getSchoolHour", "SchoolHourController@index");
Route::post("/deleteSchoolHour", "SchoolHourController@delete");
Route::put("/updateSchoolHour/{id}", "SchoolHourController@update");
/*SchoolProgramHours*/

/*Program Types*/

Route::post("/addProgramTypes", "PTypeController@store");
Route::get("/getAllProgramTypes", "PTypeController@index");

/*Program Types*/

/*Lesson*/
Route::post('/getLesson', 'LessonController@getLesson');
Route::post('/lesson', 'LessonController@store');
Route::delete('/lesson/{id}', 'LessonController@destroy');
Route::put('/lesson/{id}', 'LessonController@update');
Route::get("/getAllLessons", "LessonController@getAllLessons");


Route::put("updateSubLesson/{id}", "LessonController@updateSubLesson");
Route::delete("deleteSubLesson/{id}", "LessonController@deleteSubLesson");
Route::post('/addSubLessons', 'LessonController@addSubLessons');

/*Lesson*/

/*Branches*/
Route::post('/getBranches', 'BranchesController@getBranches');
Route::post('/branches', 'BranchesController@store');
Route::delete('/branches/{id}', 'BranchesController@destroy');
Route::put('/branches/{id}', 'BranchesController@update');
Route::get('/getAllBranches', 'BranchesController@getAllBranches');
/*Branches*/

/*Units*/
Route::post('/getUnits', 'UnitsController@getUnits');
Route::post('/units', 'UnitsController@store');
Route::delete('/units/{id}', 'UnitsController@destroy');
Route::put('/units/{id}', 'UnitsController@update');
Route::get('/getAllUnits', 'UnitsController@getAllUnits');
/*Units*/


/*School_Clases_Branches_Pivot*/


/*School_Clases_Branches_Pivot*/


Route::post("/getAllSCB", "SchoolClasesBranchesPivotController@getAllSCB");
Route::post('/schoolclasesbranchespivot', 'SchoolClasesBranchesPivotController@store');
Route::post("/getSchoolBranchClases", 'SchoolClasesBranchesPivotController@index');
Route::delete('/schoolclasesbranchespivot/{id}', 'SchoolClasesBranchesPivotController@destroy');
/*School_Clases_Branches_Pivot*/


/*School Clases Pivot */
Route::post("/getSchoolClases", "SchoolClasesPivotController@getClases");
Route::post('/schoolclasespivot', 'SchoolClasesPivotController@store');
Route::post("/getschoolclasespivot", 'SchoolClasesPivotController@index');
Route::post('/deleteschoolclasespivot/{id}', 'SchoolClasesPivotController@destroy');


Route::post("/getSchoolClases", "SchoolClasesPivotController@getClases");
Route::post('/schoolclasespivot', 'SchoolClasesPivotController@store');
Route::post("/getschoolclasespivot", 'SchoolClasesPivotController@index');
Route::post('/deleteschoolclasespivot/{id}', 'SchoolClasesPivotController@destroy');
Route::get("/getAllSchoolClases", "SchoolClasesPivotController@getAllSchoolClases");

/*School Clases Pivot */


/*ActivityUserLessons*/
Route::post("addActivityUserLessons", "ActivityUserLessonsController@store");
Route::post("/getActivityUserLessons", "ActivityUserLessonsController@index");
Route::post("/deleteActivityUserLessons", "ActivityUserLessonsController@delete");
/*ActivityUserLessons*/

/*ActivityUserClases*/
Route::post("/addActivityUserClases", "ActivityUserClasesController@store");
Route::post("/getActivityUserClases", "ActivityUserClasesController@index");
Route::post("/deleteActivityUserClases", "ActivityUserClasesController@delete");
/*ActivityUserClases*/


/*ActivitUserPeriod*/
Route::post("/addActivityUserPeriod", "ActivityUserPeriodController@store");
Route::post("/getActivityUserPeriod/{id}", "ActivityUserPeriodController@index");
Route::post("/deleteActivityUserPeriod", "ActivityUserPeriodController@delete");
/*ActivitUserPeriod*/

/*ActivityUser Pivot */
Route::post("/addActivityUser", "ActivityUserController@store");
Route::post("/getActivityUser/{id}", "ActivityUserController@index");
Route::post("/deleteActivityUser", "ActivityUserController@delete");
/*ActivityUser Pivot */


/*School Lessons Pivot*/
Route::post('/schoollessonspivot', 'SchoolLessonsPivotController@store');
Route::post("/getschoollessonspivot", 'SchoolLessonsPivotController@index');
Route::post("/getallschoollessonspivot", "SchoolLessonsPivotController@getall");
Route::post('/deleteschoollessonspivot/{id}', 'SchoolLessonsPivotController@destroy');
Route::post("/getAllSL", "SchoolLessonsPivotController@getAllSL");

/*School Lessons Pivot*/

/*School_Lessons_Clases_Pivot*/
Route::post('/schoollessonclasespivot', 'SchoolLessonsClasesPivotController@store');
Route::post("/getschoollessonclasespivot", 'SchoolLessonsClasesPivotController@index');
Route::delete('/schoollessonclasespivot/{id}', 'SchoolLessonsClasesPivotController@destroy');
/*School_Lessons_Clases_Pivot*/

/*Post*/
Route::get("/posttest", "PostController@test");
Route::post("/getPost", "PostController@index");
Route::post("/post", "PostController@create");
Route::delete("/post/{id}", "PostController@delete");
Route::post("/postlike", "PostController@postlike");
Route::get("/postlike/{id}", "PostController@getpostlikeuser");
Route::post("/mypost", "PostController@getUserPosts");
Route::get("/postview/{id}", "PostController@getpostview");
Route::get("/postdetail/{id}", "PostController@show");
Route::post("/getcomments", "PostController@getComments");
/*Post*/


/*Comment*/
Route::post("/likecomment", "CommentController@commentlike");
Route::post("/createcomment", "CommentController@store");
Route::delete("/deletecomment/{id}", "CommentController@delete");
/*Comment*/
/*PostType*/
Route::get("/posttype", "PostTypeController@index");

/*PostType*/

/*HomeWork Category*/
Route::get("/homeworktype", "HomeWorkTypeController@index");

/*Home Work Category*/

/*PostTag*/
Route::get("/posttag", "PostTagController@index");
Route::delete("/posttag/{id}", "PostTagController@delete");
Route::post("/posttag", "PostTagController@store");
Route::put("/posttag/{id}", "PostTagController@update");
/*PostTag*/

/*Post Notification*/
Route::post("/getNotification", "PostNotificationController@index");
Route::post("/getAllNotification", "PostNotificationController@all");
Route::post("/setRead", "PostNotificationController@update");
/*Post Notification*/

/*HomeWorkCategory*/
Route::delete("/homeworkcategory/{id}", "HomeWorkCategoryController@delete");
Route::post("/homeworkcategory", "HomeWorkCategoryController@store");
Route::get("/homeworkcategory", "HomeWorkCategoryController@index");
Route::put("/homeworkcategory/{id}", "HomeWorkCategoryController@update");
/*HomeWorkCategory*/

Route::post("/mesaj", "PostController@mesaj");
Route::post("/tpost", "PostController@testet");


/*Special Notes*/
Route::post("/contactlist", "SpecialNotesController@index");
Route::post("/createmessage", "SpecialNotesController@store");
Route::delete("/deletemessage/{id}", "SpecialNotesController@destroy");
Route::post("/updatemessage", "SpecialNotesController@readMsg");
Route::post("/showusermessage", "SpecialNotesController@showUserMsg");
Route::post("/usermsgbox","SpecialNotesController@msgBox");
Route::post("/deleteusermsgbox","SpecialNotesController@deleteMsgBox");
/*Special Notes*/

<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepository;
use App\Eloquent\Book;

class UserRepositoryEloquent extends AbstractRepositoryEloquent implements UserRepository
{
    public function model()
    {
        return new \App\Eloquent\User;
    }

    public function getCurrentUser($userFromAuthServer)
    {
        $userInDatabase = $this->model()->whereEmail($userFromAuthServer['email'])->first();
        $currentUser = $userInDatabase;

        if (!count($userInDatabase)) {
            $currentUser = $this->model()->create([
                'name' => $userFromAuthServer['name'],
                'email' => $userFromAuthServer['email'],
                'avatar' => $userFromAuthServer['avatar'],
            ])->fresh();
        }

        return $currentUser;
    }

    public function getDataBookByCurrentUser($action, $select = ['*'], $with = [])
    {
        if (in_array($action, array_keys(config('model.book_user.status')))) {
            return $this->getDataBookOfUser(config('model.book_user.status.' . $action), $select, $with);
        }

        if ($action == config('model.user_sharing_book')) {
            return $this->user->owners()
                ->select($select)
                ->with($with)
                ->paginate(config('paginate.default'));
        }
    }

    protected function getDataBookOfUser($status, $select = ['*'], $with = [])
    {
        if (in_array($status, array_values(config('model.book_user.status')))) {
            return $this->user->books()
                ->select($select)
                ->with($with)
                ->wherePivot('status', $status)
                ->paginate(config('paginate.default'));
        }
    }

    public function addTags(string $tags = null)
    {
        $this->user->update([
            'tags' => $tags,
        ]);
    }

    public function getInterestedBooks($dataSelect = ['*'], $with = [])
    {
        if ($this->user->tags) {
            $tags = explode(',', $this->user->tags);

            return app(Book::class)
                ->getLatestBooks($dataSelect, $with)
                ->whereIn('category_id', $tags)
                ->paginate(config('model.book.interested_books.books_per_page'));
        } else {
            return app(Book::class)
                ->getLatestBooks($dataSelect, $with)
                ->paginate(config('model.book.interested_books.books_per_page'));
        }
    }

    public function show($id)
    {
        return $this->model()->findOrFail($id);
    }
}

<?php

    namespace Cactus\Model;

    abstract class Extension
    {

        /**
         * The attributes that are mass assignable.
         * 
         * @var array
         */
        protected $fillable = null;

        /**
         * The attributes that aren't mass assignable.
         *
         * @var array
         */
        protected $guarded = null;

        /**
         * Default values for the attributes.
         *
         * @var array
         */
        protected $attributes = null;

    }
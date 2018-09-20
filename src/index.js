import 'jquery';
import './style.scss';

import Router from './util/Router';
import common from './routes/common';
import home from './routes/home';
import currentraces from './routes/currentraces';
import statpage from './routes/statpage';
import ff4festats from './routes/ff4festats';

/**
 * Populate Router instance with DOM routes
 * @type {Router} routes - An instance of our router
 */
const routes = new Router({
  /** All pages */
  common,
  /** Home page */
  home,
  /** About Us page, note the change from about-us to aboutUs. */
  currentraces,
  statpage,
  ff4festats
});

/** Load Events */
jQuery(document).ready(() => routes.loadEvents());

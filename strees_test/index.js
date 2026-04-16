import http from 'k6/http';
import { sleep } from 'k6';

export const options = {
  vus: 1000,
  duration: '3s',
}

export default function () {
  http.get('http://localhost:9000/#/login');
  sleep(1);
}


import React, {useRef, useState} from 'react';
import PropTypes from 'prop-types';
import { Preloader } from '../../../app-ui/main';
import { ticketHeader } from '../../const';

/**
 * Инпут Поиск пользователя
 * @component
 * @param {Array=} usersInput Массив передаваемых внутрь Пользователей для фильтрации уже найденных
 * @param {Object=} nesting Проверяем вложен ли массив пользователей в массив Data
 * @param {Function=} setUsersInput Функция запишет результат своей работы и отдаст на верхний уровень данные
 * @param {Object=} formOptions Объект хранит в себе информацию на тот случай, если потребуется записать пользователя в БД конкретным методом
 * @return {JSX.Element}
 */

export default function FieldUserSearch({
  usersInput = [], nesting = {}, setUsersInput = null, formOptions = {
    fields: [], url: '', method: '', headers: '', nesting: false,
  },
}) {

  const ids = [];

  usersInput.forEach((user) => {
    ids.push(user.id);
  });

  // Поиск: Пользователи
  const [users, setUsers] = useState([]);

  // Состояние: Статусы загрузки
  const [status, setStatus] = useState({ code: 200 });

  // Очистка интпута после клика на крестик
  const input = useRef(null);

  const onClear = (u) => {
    setUsers(u || []);
    input.current.value = '';
  };

  // Ищим пользователя
  async function eventKeyDown(e) {
    if (e.currentTarget.value.length && e.currentTarget.value.length > 1) {
      setStatus({ code: 99 });

      const formData = new FormData();
      formData.append('search_term', e.currentTarget.value);

      const response = await fetch('https://api.ru/api/test', {
        method: 'POST',
        body: formData,
        headers: ticketHeader('application/json'),
      });

      const result = await response.json();

      if (result.status && result.status.code === 200) {
        setUsers(result.data);
        setStatus(result.status);
      }
    } else {
      setUsers([]);
    }
  }

  // Добавляем найденного пользователя в список
  async function addUser(user) {
    setUsers([]);

    if (formOptions && formOptions.url) {
      const formData = new FormData();

      formOptions.fields.forEach((doc) => {
        formData.append(doc.field, doc.val);
      });

      formData.append('user_id', user.id);

      const response = await fetch(formOptions.url, {
        method: formOptions.method,
        body: formData,
        headers: formOptions.headers,
      });

      const result = await response.json();

      if (result.status && result.status.code === 200) {
        if (nesting && nesting.data) {
          setUsersInput({ ...nesting, ...{ data: [...nesting.data, user] } });
        } else {
          setUsersInput((current) => [...current, user]);
        }
      }
    } else if (nesting && nesting.data) {
      setUsersInput({ ...nesting, ...{ data: [...nesting.data, user] } });
    } else {
      setUsersInput((current) => [...current, user]);
    }

    // Очищаем инпут и массив найденных пользователей
    onClear();
  }

  return (
    <div
      className="row --g-2"
    >
      <div className="field col --g-2" style={{ position: 'relative', width: '100%' }}>
        <div className="field--input">
          <input
            type="text"
            style={{ minWidth: 'auto' }}
            className="input-text"
            ref={input}
            placeholder="Введите фамилию..."
            onKeyUp={(event) => eventKeyDown(event)}
          />
        </div>
        {users && users.length > 0 && (
          <button type="button" style={{ position: 'absolute', right: 0, top: 0, margin: 0, border: 0, background: 0 }} className="button --solid" onClick={() => onClear()}>
            <i className="fas fa-xmark font--danger" />
          </button>
        )}
        {status && status.code === 200 ? (
          <>
            {users && users.length > 0 && (
              <div
                className="col box p --g-2"
                style={{
                  width: '100%',
                  overflow: 'auto',
                  height: '250px',
                  position: 'absolute',
                  top: '100%',
                }}
              >
                {users.filter((item) => !ids.includes(item.id)).map((user) => (
                  <div className="button cell-auto row --center --h --g-2" style={{ display: 'block' }} onClick={() => addUser(user)}>
                    <i className="fas fa-check font--green" />
                    {' '}
                    <span title={`(${user.id}) ${user.name} ${user.lastname}`}>
                      {user.name}
                      {' '}
                      {user.lastname}
                    </span>
                  </div>
                ))}
              </div>
            )}
          </>
        ) : (
          <Preloader />
        )}
      </div>
    </div>
  );
}

FieldUserSearch.defaultProps = {
  usersInput: [],
  nesting: {},
  setUsersInput: null,
  formOptions: {},
};

FieldUserSearch.propTypes = {
  usersInput: PropTypes.arrayOf(),
  nesting: PropTypes.shape(),
  setUsersInput: PropTypes.func,
  formOptions: PropTypes.shape(PropTypes.shape()),
};

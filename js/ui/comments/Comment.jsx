import React, { useContext } from 'react';
import dayjs from 'dayjs';

import PropTypes from 'prop-types';
import { UserIcon } from '../../users/main';
import { GetListsContext } from '../Provider.jsx';
import Files from '../Files.jsx';
import { getCurrentUser } from '../../const';

/**
 * Контекст
 * @component
 * @param {Object=} document
 * @return {JSX.Element}
 */

export default function Comment({ document = {} }) {

  // Получаем списки
  const getList = useContext(GetListsContext);
  const { users } = getList;

  const createdBy = users.filter((u) => u.id === document.created_by)[0];
  const currentUserObj = users.filter((u) => u.id === getCurrentUser.serviceDeskId)[0];
  const toLeft = currentUserObj.id === createdBy.id;

  // Стили
  const styles = {
    comment: {
      maxWidth: '70%',
      overflow: 'hidden',
    },
    commentToLeft: {
      marginLeft: 'auto',
    },
    commentText: {
      backgroundColor: !toLeft ? '#f1f1f1' : '#ccdaee',
      borderRadius: 6,
    },
  };

  return (
    <div className="row --h" style={{ ...styles.comment, ...!toLeft || styles.commentToLeft }}>

      {!toLeft && (
        <div className="cell-auto">
          <UserIcon doc={createdBy} />
        </div>
      )}

      <div className="col">
        <div className="cell" style={{ textAlign: !toLeft ? 'left' : 'right' }}>
          {!toLeft ? (
            <>
              <small style={{ marginRight: 6 }}>{dayjs(document.created_at).format('DD MMM YYYY HH:mm')}</small>
              {`${createdBy.name} ${createdBy.lastname}`}
            </>
          ) : (
            <>
              {`${createdBy.name} ${createdBy.lastname}`}
              <small style={{ marginLeft: 6 }}>{dayjs(document.created_at).format('DD MMM YYYY HH:mm')}</small>
            </>
          )}
        </div>
        <div className="cell p" style={styles.commentText}>
          {document.text}
        </div>
        {!!document.file_attachments && document.file_attachments.length > 0 && (
          <div>
            <Files docs={document.file_attachments} />
          </div>
        )}
      </div>
      
      {!!toLeft && (
        <div className="cell-auto">
          <UserIcon doc={currentUserObj} />
        </div>
      )}
    </div>
  );
}

Comment.defaultProps = {
  document: {},
};

Comment.propTypes = {
  document: PropTypes.shape(),
};

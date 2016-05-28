<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 01:08
 */

namespace Saya\Core\IRC;


interface Errors
{
    const   /* Errors */
        ERR_NO_SUCH_NICK = 401, // <nickname> :No such nick
        ERR_NO_SUCH_SERVER = 402, // <server> :No such server
        ERR_NO_SUCH_CHANNEL = 403, // <channel> :No such channel
        ERR_CANNOT_SEND_TO_CHAN = 404, // <channel> :Cannot send to channel
        ERR_TOO_MANY_CHANNELS = 405, // <channel> :You have joined too many channels
        ERR_WAS_NO_SUCH_NICK = 406, // <nickname> :There was no such nickname
        ERR_TOO_MANY_TARGETS = 407, // <target> :Duplicate recipients. No message delivered
        ERR_NO_COLORS = 408, // <nickname> #<channel> :You cannot use colors on this channel. Not sent: <text>   DALNET
        ERR_NO_ORIGIN = 409, // :No origin specified
        ERR_NO_RECIPIENT = 411, // :No recipient given (<command>)
        ERR_NO_TEXT_TO_SEND = 412, // :No text to send
        ERR_NO_TOP_LEVEL = 413, // <mask> :No toplevel domain specified
        ERR_WILD_TOP_LEVEL = 414, // <mask> :Wildcard in toplevel Domain
        ERR_BAD_MASK = 415,
        ERR_TOO_MUCH_INFO = 416, // <command> :Too many lines in the output, restrict your query                     UNDERNET
        ERR_UNKNOWN_COMMAND = 421, // <command> :Unknown command
        ERR_NO_MOTD = 422, // :MOTD File is missing
        ERR_NO_ADMIN_INFO = 423, // <server> :No administrative info available
        ERR_FILE_ERROR = 424,
        ERR_NO_NICKNAME_GIVEN = 431, // :No nickname given
        ERR_ERRONEOUS_NICKNAME = 432, // <nickname> :Erroneous Nickname
        ERR_NICK_NAME_IN_USE = 433, // <nickname> :Nickname is already in use.
        ERR_NICK_COLLISION = 436, // <nickname> :Nickname collision KILL
        ERR_UN_AVAIL_RESOURCE = 437, // <channel> :Cannot change nickname while banned on channel
        ERR_NICK_TOO_FAST = 438, // <nick> :Nick change too fast. Please wait <sec> seconds.                         (most)
        ERR_TARGET_TOO_FAST = 439, // <target> :Target change too fast. Please wait <sec> seconds.                     DALNET/UNDERNET
        ERR_USER_NOT_IN_CHANNEL = 441, // <nickname> <channel> :They aren't on that channel
        ERR_NOT_ON_CHANNEL = 442, // <channel> :You're not on that channel
        ERR_USER_ON_CHANNEL = 443, // <nickname> <channel> :is already on channel
        ERR_NO_LOGIN = 444,
        ERR_SUMMON_DISABLED = 445, // :SUMMON has been disabled
        ERR_USERS_DISABLED = 446, // :USERS has been disabled
        ERR_NOT_REGISTERED = 451, // <command> :Register first.
        ERR_NEED_MORE_PARAMS = 461, // <command> :Not enough parameters
        ERR_ALREADY_REGISTERED = 462, // :You may not reregister
        ERR_NO_PERM_FOR_HOST = 463,
        ERR_PASSWD_MISTMATCH = 464,
        ERR_YOURE_BANNED_CREEP = 465,
        ERR_YOU_WILL_BE_BANNED = 466,
        ERR_KEY_SET = 467, // <channel> :Channel key already set
        ERR_SERVER_CAN_CHANGE = 468, // <channel> :Only servers can change that mode                                     DALNET
        ERR_CHANNEL_IS_FULL = 471, // <channel> :Cannot join channel (+l)
        ERR_UNKNOWN_MODE = 472, // <char> :is unknown mode char to me
        ERR_INVITE_ONLY_CHAN = 473, // <channel> :Cannot join channel (+i)
        ERR_BANNED_FROM_CHAN = 474, // <channel> :Cannot join channel (+b)
        ERR_BAD_CHANNEL_KEY = 475, // <channel> :Cannot join channel (+k)
        ERR_BAD_CHAN_MASK = 476,
        ERR_NICK_NOT_REGISTERED = 477, // <channel> :You need a registered nick to join that channel.                      DALNET
        ERR_BAN_LIST_FULL = 478, // <channel> <ban> :Channel ban/ignore list is full
        ERR_NO_PRIVILEGES = 481, // :Permission Denied- You're not an IRC operator
        ERR_CHAN_O_PRIVS_NEEDED = 482, // <channel> :You're not channel operator
        ERR_CANT_KILL_SERVER = 483, // :You cant kill a server!
        ERR_RESTRICTED = 484, // <nick> <channel> :Cannot kill, kick or deop channel service                      UNDERNET
        ERR_UNIQ_O_PRIVS_NEEDED = 485, // <channel> :Cannot join channel (reason)
        ERR_NO_OPER_HOST = 491, // :No O-lines for your host
        ERR_U_MODE_UNKNOWN_FLAG = 501, // :Unknown MODE flag
        ERR_USERS_DONT_MATCH = 502, // :Cant change mode for other users
        ERR_SILENCE_LIST_FULL = 511;// <mask> :Your silence list is full                                                UNDERNET/DALNET
}